<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
            $table->string('model_number', 100)->nullable()->unique()->after('name');
            $table->integer('warranty_months')->default(12)->after('cost_price');
            $table->boolean('serial_tracking')->default(false)->after('warranty_months');

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'model_number', 'warranty_months', 'serial_tracking']);
        });
    }
};
