<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            // por si faltan en este entorno (se crean sólo si no existen)
            if (!Schema::hasColumn('mlibre_orders','pack_id')) {
                $t->unsignedBigInteger('pack_id')->nullable()->index()->after('order_id');
            }
            if (!Schema::hasColumn('mlibre_orders','buyer_tax_status')) {
                $t->string('buyer_tax_status', 50)->nullable()->after('buyer_doc_number');
            }
            if (!Schema::hasColumn('mlibre_orders','ml_invoice_attached')) {
                $t->boolean('ml_invoice_attached')->default(false)->after('invoiced')->index();
            }
            if (!Schema::hasColumn('mlibre_orders','total_amount')) {
                $t->decimal('total_amount', 12, 2)->nullable()->after('date_created');
            }

            // 🚚 campos de envío que faltan (causa del error)
            if (!Schema::hasColumn('mlibre_orders','shipping_status')) {
                $t->string('shipping_status', 50)->nullable()->after('buyer_tax_status')->index();
            }
            if (!Schema::hasColumn('mlibre_orders','shipping_tracking_number')) {
                $t->string('shipping_tracking_number', 80)->nullable()->after('shipping_status');
            }
            if (!Schema::hasColumn('mlibre_orders','shipping_tracking_url')) {
                $t->string('shipping_tracking_url', 255)->nullable()->after('shipping_tracking_number');
            }
            if (!Schema::hasColumn('mlibre_orders','shipping_carrier')) {
                $t->string('shipping_carrier', 80)->nullable()->after('shipping_tracking_url');
            }
            if (!Schema::hasColumn('mlibre_orders','shipping_address')) {
                $t->string('shipping_address', 255)->nullable()->after('shipping_carrier');
            }

       
        
 
        });
    }

    public function down(): void
    {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            foreach ([
                'shipping_status','shipping_tracking_number','shipping_tracking_url',
                'shipping_carrier','shipping_address','ml_invoice_attached',
                'buyer_tax_status','pack_id','total_amount'
            ] as $col) {
                if (Schema::hasColumn('mlibre_orders', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
