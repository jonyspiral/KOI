<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlOrden extends Model
{
    protected $table = 'ml_ordenes';
    protected $primaryKey = 'id';
    public $incrementing = false; // porque el ID lo pone ML
    protected $keyType = 'int';
    public $timestamps = true;

    protected $casts = [
        'fulfilled' => 'boolean',
        'tags' => 'array',
        'date_created' => 'datetime',
        'date_closed' => 'datetime',
        'date_approved' => 'datetime',
    ];

    protected $fillable = [
        'id', 'date_created', 'date_closed', 'status', 'status_detail', 'fulfilled',
        'total_amount', 'paid_amount', 'coupon_amount', 'shipping_cost', 'transaction_amount',
        'shipping_id', 'shipping_status', 'shipping_substatus', 'shipping_mode', 'logistics_type',
        'receiver_city', 'receiver_state', 'receiver_zip',
        'buyer_id', 'buyer_nickname', 'buyer_email', 'buyer_first_name', 'buyer_last_name',
        'buyer_doc_type', 'buyer_doc_number',
        'payment_method', 'payment_type', 'installments', 'date_approved',
        'tags'
    ];

    public function items()
    {
        return $this->hasMany(MlOrdenItem::class, 'order_id');
    }
}
