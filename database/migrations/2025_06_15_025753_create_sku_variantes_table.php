<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkuVariantesTable extends Migration
{
    public function up()
    {
        Schema::create('sku_variantes', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->string('var_sku')->index();
            $table->string('cod_articulo');
            $table->string('cod_color_articulo');
            $table->string('familia')->nullable();

            $table->string('sku')->unique();
            $table->string('color')->nullable();
            $table->string('talle')->nullable();
            $table->float('precio')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('stock_ecommerce')->default(0);
            $table->integer('stock_2da')->default(0);
            $table->integer('stock_fulfillment')->default(0);

            $table->timestamps();
            $table->string('sync_status', 1)->default('N'); // N = nuevo, U = actualizado, D = eliminado, S = sincronizado
            $table->text('sync_log')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sku_variantes');
    }
}
