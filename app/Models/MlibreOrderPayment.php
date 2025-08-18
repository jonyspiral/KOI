<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlibreOrderPayment extends Model
{
    protected $fillable = [
        'mlibre_order_id','payment_id','status','payment_type','payment_method_id',
        'transaction_amount','total_paid_amount','fee_amount','installments','date_approved',
    ];
    protected $casts = ['date_approved'=>'datetime'];
}
