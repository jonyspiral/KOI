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
    $table->dropForeign(['ml_publicacion_id']);
    $table->dropColumn('ml_publicacion_id');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            //
        });
    }
};
