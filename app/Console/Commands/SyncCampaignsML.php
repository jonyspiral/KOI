<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MlVariante;
use App\Models\MlCampaign;
use App\Models\MlCampaignItem;
use App\Services\Mlibre\MlibreTokenService;
use App\Services\Mlibre\MlibreCampaignService;

class SyncCampaignsML extends Command
{
    protected $signature = 'mlibre:sync-campaigns';
    protected $description = 'Detect items in active Mercado Libre campaigns and store locally';

    public function handle()
    {
        $this->info('🔄 Syncing items with active campaigns or promotions...');

        $userId = env('MLIBRE_USER_ID');
        $accessToken = app(MlibreTokenService::class)->getValidAccessToken($userId);

        $variantes = MlVariante::whereNotNull('ml_id')->get();
        $total = $variantes->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $count = 0;

        foreach ($variantes as $variante) {
            $bar->advance();

            $result = MlibreCampaignService::detectCampaignsForItem($variante->ml_id, $accessToken, $userId);

            // Insertar campañas encontradas
            foreach ($result['campaigns'] as $camp) {
                $campania = MlCampaign::updateOrCreate(
                    ['ml_campaign_id' => $camp['id']],
                    [
                        'name'       => $camp['name'],
                        'type'       => 'DEAL',
                        'status'     => $camp['status'],
                        'start_date' => $camp['start_date'],
                        'end_date'   => $camp['finish_date'],
                    ]
                );

                MlCampaignItem::updateOrCreate(
                    [
                        'item_id'        => $variante->ml_id,
                        'ml_campaign_id' => $campania->id,
                    ],
                    [
                        'ml_variantes_id' => $variante->id,
                    ]
                );
                $count++;
            }

            // Caso especial: ítem con promoción activa pero sin campañas formales
            if (empty($result['campaigns']) && ($result['has_any'] ?? false)) {
                MlCampaignItem::updateOrCreate(
                    [
                        'item_id'         => $variante->ml_id,
                        'ml_campaign_id'  => null,
                    ],
                    [
                        'ml_variantes_id' => $variante->id,
                    ]
                );
                $count++;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ $count items linked to campaigns or detected as promoted.");
    }
}
