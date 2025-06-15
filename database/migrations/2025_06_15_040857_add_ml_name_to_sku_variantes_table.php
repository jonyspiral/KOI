<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('sku_variantes', function (Blueprint $table) {
        $table->string('ml_name')->nullable()->after('sku');
    });
}

public function down()
{
    Schema::table('sku_variantes', function (Blueprint $table) {
        $table->dropColumn('ml_name');
    });
}

};
