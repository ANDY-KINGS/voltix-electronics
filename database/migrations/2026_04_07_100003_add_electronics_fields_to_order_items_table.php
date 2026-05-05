<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('serial_number_id')->nullable()->after('subtotal');
            $table->integer('warranty_months')->nullable()->after('serial_number_id');
            $table->date('warranty_expiry')->nullable()->after('warranty_months');

            $table->foreign('serial_number_id')->references('id')->on('serial_numbers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['serial_number_id']);
            $table->dropColumn(['serial_number_id', 'warranty_months', 'warranty_expiry']);
        });
    }
};
