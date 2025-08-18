<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      if (!Schema::hasColumn('mlibre_orders','ml_invoiced_by_ml')) {
        $t->boolean('ml_invoiced_by_ml')->default(false)->index();
      }
      if (!Schema::hasColumn('mlibre_orders','ml_invoice_file_id')) {
        $t->string('ml_invoice_file_id', 190)->nullable();
      }
      if (!Schema::hasColumn('mlibre_orders','ml_invoice_checked_at')) {
        $t->timestamp('ml_invoice_checked_at')->nullable();
      }
    });
  }
  public function down(): void {
    Schema::table('mlibre_orders', function (Blueprint $t) {
      foreach (['ml_invoiced_by_ml','ml_invoice_file_id','ml_invoice_checked_at'] as $c) {
        if (Schema::hasColumn('mlibre_orders',$c)) $t->dropColumn($c);
      }
    });
  }
};

