<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ml_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->string('product_number');
            $table->string('ml_item_id')->nullable();
            $table->unsignedBigInteger('ml_variation_id')->nullable();

            $table->integer('stock_full')->default(0);
            $table->integer('stock_flex')->default(0);
            $table->timestamp('logged_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_stock_logs');
    }
};
