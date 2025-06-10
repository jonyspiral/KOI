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
       Schema::create('sku_variantes', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique(); // Ej: 3199GN36
    $table->string('var_sku')->index(); // Ej: cod_articulo + cod_color + talle
    $table->json('variation_ids')->nullable(); // Puede haber múltiples variation_id de ML
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sku_variantes');
    }
};
