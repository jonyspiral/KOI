<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlibreOrderItem extends Model
{
    protected $fillable = [
        'mlibre_order_id','ml_item_id','title','quantity','unit_price','sku',
        'net_amount','vat_rate','vat_amount','total_amount',
        'variation_text','variation',
    ];
    protected $casts = ['variation'=>'array'];
}
