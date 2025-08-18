<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // RAW (auditoría)
        if (!Schema::hasTable('mlibre_orders_raw')) {
            Schema::create('mlibre_orders_raw', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('seller_id')->index();
                $t->unsignedBigInteger('order_id')->index();
                $t->json('payload');
                $t->timestamp('pulled_at')->useCurrent();
                $t->unique(['seller_id', 'order_id'], 'uq_raw_seller_order');
            });
        }

        // CABECERA
        if (!Schema::hasTable('mlibre_orders')) {
            Schema::create('mlibre_orders', function (Blueprint $t) {
                $t->id();

                // Identificadores
                $t->unsignedBigInteger('seller_id')->index();
                $t->unsignedBigInteger('order_id')->index();
                $t->string('status', 40)->index()->nullable(); // paid|cancelled|...

                // Fechas
                $t->dateTime('date_created')->index()->nullable();
                $t->dateTime('date_closed')->index()->nullable();

                // Montos
                $t->decimal('total_amount', 12, 2)->nullable();
                $t->decimal('paid_amount', 12, 2)->nullable();
                $t->string('currency_id', 8)->nullable();

                // Comprador
                $t->unsignedBigInteger('buyer_id')->nullable()->index();
                $t->string('buyer_name', 150)->nullable();
                $t->string('buyer_doc_type', 20)->nullable();
                $t->string('buyer_doc_number', 40)->nullable()->index();

                // Envío (básico)
                $t->unsignedBigInteger('shipping_id')->nullable()->index();
                $t->string('address_line', 255)->nullable();
                $t->string('city', 120)->nullable();
                $t->string('state', 120)->nullable();
                $t->string('zip_code', 20)->nullable();

                // Métricas
                $t->unsignedSmallInteger('items_count')->default(0);
                $t->unsignedSmallInteger('payments_count')->default(0);

                // Tags u otros
                $t->json('tags')->nullable();

                // ---------- Facturación (ARCA) ----------
                $t->string('arca_status', 20)->default('pending')->index(); // pending|queued|processing|success|error
                $t->boolean('invoiced')->default(false)->index();

                $t->string('invoice_type', 3)->nullable()->index(); // A|B|C|NC|ND
                $t->unsignedMediumInteger('pos_number')->nullable()->index();
                $t->unsignedBigInteger('invoice_number')->nullable()->index();
                $t->date('invoice_date')->nullable()->index();

                $t->string('cae', 20)->nullable()->index();
                $t->date('cae_due_date')->nullable();

                $t->decimal('net_amount', 12, 2)->nullable();
                $t->decimal('vat_amount', 12, 2)->nullable();
                $t->decimal('other_taxes_amount', 12, 2)->nullable();
                $t->json('vat_breakdown')->nullable(); // [{aliquot:21, net:..., iva:...}]

                $t->string('arca_invoice_id', 64)->nullable()->index();
                $t->json('arca_payload')->nullable();
                $t->text('arca_error')->nullable();

                $t->timestamps();

                $t->unique(['seller_id', 'order_id'], 'uq_orders_seller_order');
            });
        }

        // ITEMS
        if (!Schema::hasTable('mlibre_order_items')) {
            Schema::create('mlibre_order_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('mlibre_order_id')->constrained('mlibre_orders')->cascadeOnDelete();

                $t->string('ml_item_id', 30)->nullable();
                $t->string('title', 255)->nullable();
                $t->unsignedInteger('quantity')->default(1);
                $t->decimal('unit_price', 12, 2)->nullable();
                $t->string('sku', 80)->nullable();

                // IVA por ítem (útil para A/RI)
                $t->decimal('net_amount', 12, 2)->nullable();
                $t->decimal('vat_rate', 5, 2)->nullable();   // 21.00 etc
                $t->decimal('vat_amount', 12, 2)->nullable();
                $t->decimal('total_amount', 12, 2)->nullable();

                $t->string('variation_text', 255)->nullable();
                $t->json('variation')->nullable();

                $t->timestamps();
                $t->index(['mlibre_order_id']);
            });
        }

        // PAGOS
        if (!Schema::hasTable('mlibre_order_payments')) {
            Schema::create('mlibre_order_payments', function (Blueprint $t) {
                $t->id();
                $t->foreignId('mlibre_order_id')->constrained('mlibre_orders')->cascadeOnDelete();

                $t->string('payment_id', 40)->nullable()->index();
                $t->string('status', 40)->nullable();
                $t->string('payment_type', 40)->nullable();         // credit_card / account_money / ...
                $t->string('payment_method_id', 40)->nullable();    // visa / master ...
                $t->decimal('transaction_amount', 12, 2)->nullable();
                $t->decimal('total_paid_amount', 12, 2)->nullable();
                $t->decimal('fee_amount', 12, 2)->nullable();
                $t->unsignedSmallInteger('installments')->nullable();
                $t->dateTime('date_approved')->nullable();

                $t->timestamps();
            });
        }

        // ENVÍOS
        if (!Schema::hasTable('mlibre_shipments')) {
            Schema::create('mlibre_shipments', function (Blueprint $t) {
                $t->id();
                $t->foreignId('mlibre_order_id')->constrained('mlibre_orders')->cascadeOnDelete();

                $t->unsignedBigInteger('shipment_id')->nullable()->index();
                $t->string('status', 40)->nullable();     // shipped|delivered|...
                $t->string('service', 60)->nullable();    // ME1/FULL/FLEX/etc.
                $t->string('tracking_number', 60)->nullable();

                // Dirección normalizada
                $t->string('address_line', 255)->nullable();
                $t->string('street_name', 120)->nullable();
                $t->string('street_number', 20)->nullable();
                $t->string('city', 120)->nullable();
                $t->string('state', 120)->nullable();
                $t->string('zip_code', 20)->nullable();

                $t->json('raw')->nullable();
                $t->timestamps();
            });
        }

        // LOGS ARCA
        if (!Schema::hasTable('arca_facturar_logs')) {
            Schema::create('arca_facturar_logs', function (Blueprint $t) {
                $t->id();
                $t->foreignId('mlibre_order_id')->constrained('mlibre_orders')->cascadeOnDelete();
                $t->string('status', 20)->index(); // queued|processing|success|error
                $t->unsignedSmallInteger('attempt')->default(1);
                $t->timestamp('scheduled_at')->nullable();
                $t->timestamp('sent_at')->nullable();
                $t->integer('http_code')->nullable();
                $t->json('request_payload')->nullable();
                $t->json('response_payload')->nullable();
                $t->text('error_message')->nullable();
                $t->timestamps();

                $t->index(['mlibre_order_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('arca_facturar_logs');
        Schema::dropIfExists('mlibre_shipments');
        Schema::dropIfExists('mlibre_order_payments');
        Schema::dropIfExists('mlibre_order_items');
        Schema::dropIfExists('mlibre_orders');
        Schema::dropIfExists('mlibre_orders_raw');
    }
};
