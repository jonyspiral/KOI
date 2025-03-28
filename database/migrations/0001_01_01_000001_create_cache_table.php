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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 255)->primary(); // Especificar tamaño evita que sea NVARCHAR(MAX)
            $table->string('value', 3900); // NVARCHAR(4000) genera problemas en SQL Server 2000, mejor 3900
            $table->bigInteger('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 255)->primary(); // Especificar tamaño
            $table->string('owner', 255);
            $table->bigInteger('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
