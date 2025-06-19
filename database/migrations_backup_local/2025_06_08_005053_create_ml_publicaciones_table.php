<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlPublicacionesTable extends Migration
{
    public function up()
    {
        Schema::create('ml_publicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('ml_id')->unique(); // MLAxxxx
            $table->string('ml_reference')->nullable(); // agrupador lógico
            $table->string('ml_name')->nullable();       // título editable
            $table->text('ml_description')->nullable();  // descripción editable
            $table->decimal('mlibre_precio', 10, 2)->nullable(); // precio editable
            $table->integer('mlibre_stock')->nullable();         // stock editable (total por publicación)
            $table->string('status')->nullable();        // active, paused, etc.
            $table->json('raw_json');                    // JSON original
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ml_publicaciones');
    }
}
