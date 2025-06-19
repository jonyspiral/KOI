<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlVariantesTable extends Migration
{
    public function up()
    {
        Schema::create('ml_variantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ml_publicacion_id');
            $table->string('sku_')->nullable();          // cod_articulo+color+talle
            $table->string('talle')->nullable();         // Ej. "36", "38"
            $table->decimal('precio', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->json('raw_json')->nullable();        // Guarda la variante completa
            $table->timestamps();

            $table->foreign('ml_publicacion_id')->references('id')->on('ml_publicaciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ml_variantes');
    }
}
