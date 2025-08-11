<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('ref_type_id');
            $table->integer('ref_num');
            $table->unsignedBigInteger('item_type_id');
            $table->string('product_code');
            $table->string('product_name');
            $table->unsignedBigInteger('unit_type_id');
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->decimal('discount_amount', 15, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->decimal('total_amount', 15, 2);
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_details');
    }
};
