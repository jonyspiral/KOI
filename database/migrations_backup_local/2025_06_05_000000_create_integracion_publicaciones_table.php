<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegracionPublicacionesTable extends Migration
{
    public function up()
    {
        Schema::create('integracion_publicaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cod_articulo');
            $table->string('cod_color_articulo');
            $table->string('plataforma');
            $table->string('external_id')->nullable();
            $table->string('status')->nullable();
            $table->boolean('sync_price')->default(true);
            $table->boolean('sync_stock')->default(true);
            $table->timestamp('fecha_ultima_sync')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('integracion_publicaciones');
    }
}

