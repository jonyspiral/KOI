<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTituloToMlVariantes extends Migration
{
    public function up(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            if (!Schema::hasColumn('ml_variantes', 'titulo')) {
                $table->string('titulo')->nullable()->after('modelo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            if (Schema::hasColumn('ml_variantes', 'titulo')) {
                $table->dropColumn('titulo');
            }
        });
    }
}
