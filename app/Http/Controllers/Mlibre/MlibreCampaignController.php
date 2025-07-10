<?php
namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use App\Models\MlCampaign;
use App\Models\MlCampaignItem;
use App\Models\MlVariante;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class MlibreCampaignController extends Controller
{
 

public function index(Request $request)
{
    $query = MlVariante::query()
        ->leftJoin('ml_campaign_items', 'ml_variantes.id', '=', 'ml_campaign_items.ml_variantes_id')
        ->select('ml_variantes.*', DB::raw('IF(ml_campaign_items.id IS NOT NULL, 1, 0) as en_campania'));

    if ($request->get('en_campania') == '1') {
        $query->whereNotNull('ml_campaign_items.id');
    }

    $registros = $query->paginate(50);

   return view('mlibre.campaigns.index',  ['variantes' => $registros]);

}

}
