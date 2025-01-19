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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unsigned()->nullable()->onDelete('cascade');
            $table->decimal('sub_total', 8, 2);
            $table->integer('discount')->default(0);
            $table->decimal('shipping_free', 8 ,2);
            $table->decimal('tax', 8 ,2);
            $table->decimal('total_amount', 8, 2);
            $table->enum('order_status', ['paid', 'canceled', 'failed', 'expired']);
            $table->foreignId('billing_address')->unsigned()->nullable()->onDelete('cascade');
            $table->foreignId('shipping_address')->unsigned()->nullable()->onDelete('cascade');
            $table->string('payment_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
