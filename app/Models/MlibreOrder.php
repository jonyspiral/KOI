<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlibreOrder extends Model
{
    protected $fillable = [
        'seller_id','order_id','status','date_created','date_closed',
        'total_amount','paid_amount','currency_id',
        'buyer_id','buyer_name','buyer_doc_type','buyer_doc_number',
        'shipping_id','address_line','city','state','zip_code',
        'items_count','payments_count','tags',
        'arca_status','invoiced','invoice_type','pos_number','invoice_number','invoice_date',
        'cae','cae_due_date','net_amount','vat_amount','other_taxes_amount','vat_breakdown',
        'arca_invoice_id','arca_payload','arca_error',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'date_closed'  => 'datetime',
        'invoice_date' => 'date',
        'vat_breakdown'=> 'array',
        'tags'         => 'array',
        'invoiced'     => 'boolean',
    ];

    public function items()     { return $this->hasMany(MlibreOrderItem::class); }
    public function payments()  { return $this->hasMany(MlibreOrderPayment::class); }
    public function shipments() { return $this->hasMany(MlibreShipment::class); }
    
public function logs()
{
    // FK en arca_facturar_logs = mlibre_order_id (interno)
    // Local key en mlibre_orders = id (interno)
    return $this->hasMany(\App\Models\ArcaFacturarLog::class, 'mlibre_order_id', 'id');
}

    // Scopes
    public function scopePaid($q)         { return $q->where('status','paid'); }
    public function scopeNotInvoiced($q)  { return $q->where('invoiced', false); }
}
