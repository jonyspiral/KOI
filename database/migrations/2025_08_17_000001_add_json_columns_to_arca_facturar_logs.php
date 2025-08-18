<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arca_facturar_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('arca_facturar_logs', 'request_json')) {
                $table->longText('request_json')->nullable()->after('status');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'response_json')) {
                $table->longText('response_json')->nullable()->after('request_json');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'error_message')) {
                $table->longText('error_message')->nullable()->after('response_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arca_facturar_logs', function (Blueprint $table) {
            if (Schema::hasColumn('arca_facturar_logs', 'request_json')) {
                $table->dropColumn('request_json');
            }
            if (Schema::hasColumn('arca_facturar_logs', 'response_json')) {
                $table->dropColumn('response_json');
            }
            if (Schema::hasColumn('arca_facturar_logs', 'error_message')) {
                $table->dropColumn('error_message');
            }
        });
    }
};
