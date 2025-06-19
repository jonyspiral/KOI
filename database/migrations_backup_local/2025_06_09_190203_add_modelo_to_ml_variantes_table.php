<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('ml_variantes', function (Blueprint $table) {
        $table->string('modelo')->nullable()->after('color');
    });
}

public function down()
{
    Schema::table('ml_variantes', function (Blueprint $table) {
        $table->dropColumn('modelo');
    });
}

};
