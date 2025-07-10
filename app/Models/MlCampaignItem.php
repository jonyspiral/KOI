<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlCampaignItem extends Model
{
    protected $table = 'ml_campaign_items';

    protected $fillable = [
        'ml_campaign_id',
        'item_id',
        'ml_variantes_id',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MlCampaign::class, 'ml_campaign_id');
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(MlVariante::class, 'ml_variantes_id');
    }
    
}
