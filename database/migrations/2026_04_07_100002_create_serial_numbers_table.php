<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('serial_number', 100)->unique();
            $table->enum('status', ['available', 'sold', 'returned'])->default('available');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_numbers');
    }
};
