<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->string('gateway')->nullable();
            $table->string('method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};