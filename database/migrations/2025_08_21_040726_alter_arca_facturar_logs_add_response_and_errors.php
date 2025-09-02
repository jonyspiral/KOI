<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('arca_facturar_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('arca_facturar_logs', 'response_xml')) {
                $table->longText('response_xml')->nullable()->after('request_json');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'error_code')) {
                $table->string('error_code', 32)->nullable()->after('response_xml');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'error_message')) {
                $table->text('error_message')->nullable()->after('error_code');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'obs_code')) {
                $table->string('obs_code', 32)->nullable()->after('error_message');
            }
            if (!Schema::hasColumn('arca_facturar_logs', 'obs_message')) {
                $table->text('obs_message')->nullable()->after('obs_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arca_facturar_logs', function (Blueprint $table) {
            if (Schema::hasColumn('arca_facturar_logs', 'obs_message')) $table->dropColumn('obs_message');
            if (Schema::hasColumn('arca_facturar_logs', 'obs_code'))    $table->dropColumn('obs_code');
            if (Schema::hasColumn('arca_facturar_logs', 'error_message')) $table->dropColumn('error_message');
            if (Schema::hasColumn('arca_facturar_logs', 'error_code'))  $table->dropColumn('error_code');
            if (Schema::hasColumn('arca_facturar_logs', 'response_xml'))$table->dropColumn('response_xml');
        });
    }
};
