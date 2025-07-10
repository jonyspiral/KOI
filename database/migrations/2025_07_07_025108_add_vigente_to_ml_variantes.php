<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVigenteToMlVariantes extends Migration
{
    public function up()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->boolean('vigente')->default(true)->after('sync_status');
        });
    }

    public function down()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn('vigente');
        });
    }
}
