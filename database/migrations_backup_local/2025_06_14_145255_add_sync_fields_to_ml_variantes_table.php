<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->char('sync_status', 1)->default('N')->after('stock');
            $table->text('sync_log')->nullable()->after('sync_status');
            $table->boolean('manual_price')->default(false)->after('sync_log');
            $table->boolean('manual_stock')->default(false)->after('manual_price');
        });
    }

    public function down(): void
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'sync_log', 'manual_price', 'manual_stock']);
        });
    }
};

