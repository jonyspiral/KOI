<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ml_campaign_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ml_campaign_id');
            $table->string('item_id');
            $table->unsignedBigInteger('ml_variantes_id')->nullable();
            $table->timestamps();

            $table->foreign('ml_campaign_id')->references('id')->on('ml_campaigns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_campaign_items');
    }
};
