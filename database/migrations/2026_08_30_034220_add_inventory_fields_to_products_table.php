<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('id');
            $table->string('barcode')->nullable()->unique()->after('sku');

            $table->decimal('cost_price', 12, 2)
                ->default(0)
                ->after('price');

            $table->integer('min_stock')
                ->default(5)
                ->after('quantity');

            $table->string('unit')
                ->default('pcs')
                ->after('min_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'barcode',
                'cost_price',
                'min_stock',
                'unit'
            ]);
        });
    }
};