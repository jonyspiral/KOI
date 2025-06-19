<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposExtraAMlPublicacionesYVariantes extends Migration
{
    public function up()
    {
        Schema::table('ml_publicaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('ml_publicaciones', 'category_id')) {
                $table->string('category_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('ml_publicaciones', 'family_id')) {
                $table->string('family_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('ml_publicaciones', 'family_name')) {
                $table->string('family_name')->nullable()->after('family_id');
            }
        });

        Schema::table('ml_variantes', function (Blueprint $table) {
            if (!Schema::hasColumn('ml_variantes', 'stock_flex')) {
                $table->integer('stock_flex')->nullable()->after('stock');
            }
            if (!Schema::hasColumn('ml_variantes', 'stock_full')) {
                $table->integer('stock_full')->nullable()->after('stock_flex');
            }
            if (!Schema::hasColumn('ml_variantes', 'logistic_type')) {
                $table->string('logistic_type')->nullable()->after('stock_full');
            }
            if (!Schema::hasColumn('ml_variantes', 'family_id')) {
                $table->string('family_id')->nullable()->after('logistic_type');
            }
        });
    }

    public function down()
    {
        Schema::table('ml_publicaciones', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'family_id', 'family_name']);
        });

        Schema::table('ml_variantes', function (Blueprint $table) {
            $table->dropColumn(['stock_flex', 'stock_full', 'logistic_type', 'family_id']);
        });
    }
}
