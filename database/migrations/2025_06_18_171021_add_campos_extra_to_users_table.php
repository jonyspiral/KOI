<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre_completo')->nullable()->after('name');
            $table->string('aplicacion_default')->nullable()->after('nombre_completo');
            $table->boolean('es_admin')->default(false)->after('aplicacion_default');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nombre_completo', 'aplicacion_default', 'es_admin']);
        });
    }
};
