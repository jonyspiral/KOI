<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlOrdenesTable extends Migration
{
    public function up()
    {
        Schema::create('ml_ordenes', function (Blueprint $table) {
            $table->id(); // ID ML
            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_closed')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('status_detail', 100)->nullable();
            $table->boolean('fulfilled')->default(false);

            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('coupon_amount', 12, 2)->nullable();
            $table->decimal('shipping_cost', 12, 2)->nullable();
            $table->decimal('transaction_amount', 12, 2)->nullable();

            $table->bigInteger('shipping_id')->nullable();
            $table->string('shipping_status', 50)->nullable();
            $table->string('shipping_substatus', 100)->nullable();
            $table->string('shipping_mode', 50)->nullable();
            $table->string('logistics_type', 50)->nullable();

            $table->string('receiver_city', 100)->nullable();
            $table->string('receiver_state', 100)->nullable();
            $table->string('receiver_zip', 20)->nullable();

            $table->bigInteger('buyer_id')->nullable();
            $table->string('buyer_nickname', 100)->nullable();
            $table->string('buyer_email', 150)->nullable();
            $table->string('buyer_first_name', 100)->nullable();
            $table->string('buyer_last_name', 100)->nullable();
            $table->string('buyer_doc_type', 10)->nullable();
            $table->string('buyer_doc_number', 30)->nullable();

            $table->string('payment_method', 50)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->unsignedTinyInteger('installments')->nullable();
            $table->timestamp('date_approved')->nullable();

            $table->json('tags')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ml_ordenes');
    }
}
