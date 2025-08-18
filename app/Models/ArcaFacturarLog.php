<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArcaFacturarLog extends Model
{
    protected $fillable = [
        'mlibre_order_id','status','attempt','scheduled_at','sent_at','http_code',
        'request_payload','response_payload','error_message',
    ];
    protected $casts = [
        'scheduled_at'=>'datetime',
        'sent_at'=>'datetime',
        'request_payload'=>'array',
        'response_payload'=>'array',
    ];
}
