<?php
// database/migrations/2025_08_17_235900_add_pack_and_indexes_to_mlibre.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      if (!Schema::hasColumn('mlibre_orders','pack_id')) {
        $t->unsignedBigInteger('pack_id')->nullable()->index()->after('order_id');
      }
      if (!Schema::hasColumn('mlibre_orders','buyer_tax_status')) {
        $t->string('buyer_tax_status', 50)->nullable()->after('buyer_doc_number');
      }
      if (!Schema::hasColumn('mlibre_orders','ml_invoice_attached')) {
        $t->boolean('ml_invoice_attached')->default(false)->after('invoiced')->index();
      }
      // performance
      if (!Schema::hasColumn('mlibre_orders','total_amount')) {
        $t->decimal('total_amount', 12, 2)->nullable()->after('date_created');
      }
      $t->index(['seller_id','date_created']);
      $t->index(['seller_id','status']);
    });
  }
  public function down(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      if (Schema::hasColumn('mlibre_orders','pack_id')) $t->dropColumn('pack_id');
      if (Schema::hasColumn('mlibre_orders','buyer_tax_status')) $t->dropColumn('buyer_tax_status');
      if (Schema::hasColumn('mlibre_orders','ml_invoice_attached')) $t->dropColumn('ml_invoice_attached');
    });
  }
};
