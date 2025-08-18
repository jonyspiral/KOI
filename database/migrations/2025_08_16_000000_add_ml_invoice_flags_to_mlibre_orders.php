// database/migrations/2025_08_16_000000_add_ml_invoice_flags_to_mlibre_orders.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            $t->boolean('ml_invoice_attached')->default(false)->index();
            $t->unsignedInteger('ml_invoice_docs_count')->default(0);
            $t->timestamp('ml_invoice_synced_at')->nullable();
        });
    }

    public function down(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            $t->dropColumn(['ml_invoice_attached','ml_invoice_docs_count','ml_invoice_synced_at']);
        });
    }
};
