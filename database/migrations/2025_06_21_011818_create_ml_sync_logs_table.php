<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('ml_sync_logs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('ml_variante_id')->nullable();
    $table->string('campo'); // 'stock' o 'precio'
    $table->boolean('exito')->default(false);
    $table->text('mensaje')->nullable();
    $table->timestamps();

    $table->foreign('ml_variante_id')->references('id')->on('ml_variantes')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_sync_logs');
    }
};
