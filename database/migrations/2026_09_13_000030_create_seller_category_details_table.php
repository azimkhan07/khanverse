<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_category_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->unique()->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_category_details');
    }
};