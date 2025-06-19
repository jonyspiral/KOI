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
        Schema::table('ml_variantes', function (Blueprint $table) {
    $table->string('ml_id')->nullable()->after('id');
    $table->unsignedBigInteger('variation_id')->nullable()->unique()->after('ml_id');
    $table->string('seller_custom_field_actual')->nullable()->after('stock');
    $table->string('var_sku_sugerido')->nullable()->after('seller_custom_field_actual');
    $table->string('nuevo_seller_custom_field')->nullable()->after('var_sku_sugerido');
    $table->boolean('sincronizado')->default(false)->after('nuevo_seller_custom_field');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_structure', function (Blueprint $table) {
            //
        });
    }
};
