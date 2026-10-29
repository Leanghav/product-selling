<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_no')->unique();

            $table->string('type');
            // sale, purchase, customer, supplier

            $table->nullableMorphs('payable');

            $table->decimal('amount', 12, 2);

            $table->string('payment_method')->default('cash');

            $table->date('payment_date');

            $table->text('note')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};