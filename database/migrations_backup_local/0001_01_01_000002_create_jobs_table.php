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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue', 255)->index();
            $table->string('payload', 3900); // Evita NVARCHAR(MAX)
            $table->smallInteger('attempts'); // Reemplaza TINYINT UNSIGNED
            $table->integer('reserved_at')->nullable();
            $table->integer('available_at');
            $table->integer('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('name', 255);
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->string('failed_job_ids', 3900); // Evita NVARCHAR(MAX)
            $table->string('options', 255)->nullable(); // Reemplazo de mediumText()
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 255)->unique();
            $table->string('connection', 255); // Reemplazo de text()
            $table->string('queue', 255); // Reemplazo de text()
            $table->string('payload', 3900); // Evita NVARCHAR(MAX)
            $table->string('exception', 3900); // Evita NVARCHAR(MAX)
            $table->integer('failed_at'); // SQL Server 2000 no admite timestamp por defecto
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
