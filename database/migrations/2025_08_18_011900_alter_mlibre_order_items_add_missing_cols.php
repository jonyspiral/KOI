<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mlibre_order_items', function (Blueprint $t) {
            // Clave foránea ya debería existir: mlibre_order_id (BIGINT UNSIGNED)

            // item_id de ML es alfanumérico (ej. MLA123...), debe ser VARCHAR
            if (!Schema::hasColumn('mlibre_order_items', 'item_id')) {
                $t->string('item_id', 30)->nullable()->after('mlibre_order_id');
                $t->index('item_id');
            } else {
                // Si existiera pero con otro tipo, podrías adaptarlo aquí (opcional):
                // $t->string('item_id', 30)->change();
            }

            if (!Schema::hasColumn('mlibre_order_items', 'title')) {
                $t->string('title', 255)->nullable()->after('item_id');
            }

            if (!Schema::hasColumn('mlibre_order_items', 'variation_text')) {
                $t->string('variation_text', 255)->nullable()->after('title');
            }

            if (!Schema::hasColumn('mlibre_order_items', 'quantity')) {
                $t->unsignedSmallInteger('quantity')->default(1)->after('variation_text');
            }

            if (!Schema::hasColumn('mlibre_order_items', 'unit_price')) {
                $t->decimal('unit_price', 12, 2)->nullable()->after('quantity');
            }

            // Si faltaran timestamps (por seguridad)
            if (!Schema::hasColumn('mlibre_order_items', 'created_at')) {
                $t->timestamp('created_at')->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('mlibre_order_items', 'updated_at')) {
                $t->timestamp('updated_at')->nullable()->after('created_at');
            }

            // Índices útiles (solo si faltaran):
            if (!Schema::hasColumn('mlibre_order_items', 'mlibre_order_id')) {
                // En caso extremo de que no exista, lo declaras:
                // $t->unsignedBigInteger('mlibre_order_id')->after('id');
                // $t->index('mlibre_order_id');
            } else {
                // Asegurar índice simple por si no existe (no falla si ya hay índice compuesto)
                // Nota: Laravel no expone "hasIndex" fácil; como es opcional, lo omitimos para evitar 1061.
            }
        });
    }

    public function down(): void
    {
        Schema::table('mlibre_order_items', function (Blueprint $t) {
            // Solo eliminar lo agregado por esta migración (si quisieras revertir)
            foreach (['item_id','title','variation_text','quantity','unit_price'] as $col) {
                if (Schema::hasColumn('mlibre_order_items', $col)) {
                    $t->dropColumn($col);
                }
            }
            // Timestamps no los removemos para no perder metadatos
        });
    }
};
