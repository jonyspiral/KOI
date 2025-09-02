<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();
        return (bool) DB::selectOne(
            'SELECT 1 FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, $table, $indexName]
        );
    }

    public function up(): void
    {
        if (!Schema::hasTable('mlibre_orders_raw')) {
            Schema::create('mlibre_orders_raw', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->unsignedBigInteger('seller_id');
                $t->unsignedBigInteger('order_id');
                // Usamos LONGTEXT para evitar dependencia de JSON/DBAL
                $t->longText('payload')->nullable();
                $t->timestamps(); // created_at / updated_at

                $t->unique(['seller_id','order_id'], 'uq_raw_seller_order');
                $t->index('seller_id');
                $t->index('order_id');
            });
            return;
        }

        // Tabla existe: agregamos columnas que falten
        Schema::table('mlibre_orders_raw', function (Blueprint $t) {
            if (!Schema::hasColumn('mlibre_orders_raw', 'seller_id')) {
                $t->unsignedBigInteger('seller_id')->after('id');
                $t->index('seller_id');
            }
            if (!Schema::hasColumn('mlibre_orders_raw', 'order_id')) {
                $t->unsignedBigInteger('order_id')->after('seller_id');
                $t->index('order_id');
            }
            if (!Schema::hasColumn('mlibre_orders_raw', 'payload')) {
                $t->longText('payload')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('mlibre_orders_raw', 'created_at')) {
                $t->timestamp('created_at')->nullable()->after('payload');
            }
            if (!Schema::hasColumn('mlibre_orders_raw', 'updated_at')) {
                $t->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Unicidad (si no existiera)
        if (!$this->indexExists('mlibre_orders_raw', 'uq_raw_seller_order')) {
            Schema::table('mlibre_orders_raw', function (Blueprint $t) {
                $t->unique(['seller_id','order_id'], 'uq_raw_seller_order');
            });
        }
    }

    public function down(): void
    {
        // No borramos la tabla. Si querés rollback fino, podrías dropear índices/cols.
    }
};

