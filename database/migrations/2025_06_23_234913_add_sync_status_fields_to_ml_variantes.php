<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncStatusFieldsToMlVariantes extends Migration
{
    public function up()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->string('sync_status_stock', 1)->nullable()->after('sync_status');
            $table->string('sync_status_precio', 1)->nullable()->after('sync_status_stock');
        });
    }

    public function down()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn(['sync_status_stock', 'sync_status_precio']);
        });
    }
}

