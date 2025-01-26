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
        Schema::create('store_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->boolean('main')->default(1);
            $table->string('branch_number');
            $table->string('street');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->string('region');
            $table->string('zipcode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_addresses');
    }
};
