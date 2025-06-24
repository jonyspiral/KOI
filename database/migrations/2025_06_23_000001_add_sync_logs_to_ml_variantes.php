<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncLogsToMlVariantes extends Migration
{
    public function up(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->text('sync_log_stock')->nullable()->after('sync_log');
            $table->text('sync_log_precio')->nullable()->after('sync_log_stock');
        });
    }

    public function down(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn('sync_log_stock');
            $table->dropColumn('sync_log_precio');
        });
    }
}
