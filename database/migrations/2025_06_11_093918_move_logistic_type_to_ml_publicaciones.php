<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveLogisticTypeToMlPublicaciones extends Migration
{
    public function up(): void
    {
        // Agregar el campo a ml_publicaciones
        Schema::table('ml_publicaciones', function (Blueprint $table) {
            $table->string('logistic_type')->nullable()->after('category_id');
        });

        // Eliminar el campo de ml_variantes
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn('logistic_type');
        });
    }

    public function down(): void
    {
        // Restaurar el campo en ml_variantes
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->string('logistic_type')->nullable()->after('stock');
        });

        // Eliminar el campo de ml_publicaciones
        Schema::table('ml_publicaciones', function (Blueprint $table) {
            $table->dropColumn('logistic_type');
        });
    }
}
