<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlCampaign extends Model
{
    protected $table = 'ml_campaigns';

    protected $fillable = [
        'ml_campaign_id',
        'name',
        'type',
        'status',
        'start_date',
        'end_date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MlCampaignItem::class, 'ml_campaign_id');
    }
}
