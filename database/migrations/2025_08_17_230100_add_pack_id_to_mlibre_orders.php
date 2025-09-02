<?php
// database/migrations/2025_08_17_230100_add_pack_id_to_mlibre_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      if (!Schema::hasColumn('mlibre_orders','pack_id')) {
        $t->unsignedBigInteger('pack_id')->nullable()->index()->after('order_id');
      }
    });
  }
  public function down(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      if (Schema::hasColumn('mlibre_orders','pack_id')) $t->dropColumn('pack_id');
    });
  }
};
