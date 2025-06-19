<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeloAndSellerSkuToMlVariantesTable extends Migration
{
    public function up()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            if (!Schema::hasColumn('ml_variantes', 'modelo')) {
                $table->string('modelo')->nullable()->after('variation_id');
            }

            if (!Schema::hasColumn('ml_variantes', 'seller_sku')) {
                $table->string('seller_sku')->nullable()->after('modelo');
            }
        });
    }

    public function down()
    {
        Schema::table('ml_variantes', function (Blueprint $table) {
            if (Schema::hasColumn('ml_variantes', 'modelo')) {
                $table->dropColumn('modelo');
            }

            if (Schema::hasColumn('ml_variantes', 'seller_sku')) {
                $table->dropColumn('seller_sku');
            }
        });
    }
}
