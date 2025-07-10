<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlOrdenItemsTable extends Migration
{
    public function up()
    {
        Schema::create('ml_orden_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // FK

            $table->string('ml_id', 50)->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->string('seller_custom_field', 100)->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('full_unit_price', 12, 2)->nullable();
            $table->string('currency', 5)->nullable();
            $table->string('category_id', 50)->nullable();
            $table->text('permalink')->nullable();
            $table->json('attributes')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('ml_ordenes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ml_orden_items');
    }
}
