<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
        $table->id();
        $table->string('po_buyer_no');
        $table->integer('order_type_id');
        $table->date('order_date');
        $table->date('shipping_date');
        $table->integer('customer_id');
        $table->integer('currency_id');
        $table->string('email');
        $table->string('phone', 20);
        $table->decimal('exchange_rate', 15, 2);
        $table->integer('pph');
        $table->integer('status_id');
        $table->boolean('vat');
        $table->text('buyer_address');
        $table->decimal('sub_amount', 15, 2);
        $table->decimal('total_discount', 15, 2);
        $table->decimal('after_discount', 15, 2);
        $table->decimal('total_vat', 15, 2);
        $table->decimal('total_pph', 15, 2);
        $table->decimal('grand_total', 15, 2);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
