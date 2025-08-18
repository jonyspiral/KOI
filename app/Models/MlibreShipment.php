<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlibreShipment extends Model
{
    protected $fillable = [
        'mlibre_order_id','shipment_id','status','service','tracking_number',
        'address_line','street_name','street_number','city','state','zip_code','raw',
    ];
    protected $casts = ['raw'=>'array'];
}

