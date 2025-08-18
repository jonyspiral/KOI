<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            if (!Schema::hasColumn('mlibre_orders','ml_invoice_attached')) {
                $t->boolean('ml_invoice_attached')->default(false)->index();
            }
            if (!Schema::hasColumn('mlibre_orders','ml_invoice_docs_count')) {
                $t->unsignedInteger('ml_invoice_docs_count')->default(0);
            }
            if (!Schema::hasColumn('mlibre_orders','ml_invoice_synced_at')) {
                $t->timestamp('ml_invoice_synced_at')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            if (Schema::hasColumn('mlibre_orders','ml_invoice_attached')) $t->dropColumn('ml_invoice_attached');
            if (Schema::hasColumn('mlibre_orders','ml_invoice_docs_count')) $t->dropColumn('ml_invoice_docs_count');
            if (Schema::hasColumn('mlibre_orders','ml_invoice_synced_at')) $t->dropColumn('ml_invoice_synced_at');
        });
    }
};
