<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlOrdenItem extends Model
{
    protected $table = 'ml_orden_items';
    protected $fillable = [
        'order_id', 'ml_id', 'variation_id', 'seller_custom_field',
        'title', 'quantity', 'unit_price', 'full_unit_price', 'currency',
        'category_id', 'permalink', 'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function orden()
    {
        return $this->belongsTo(MlOrden::class, 'order_id');
    }
}
