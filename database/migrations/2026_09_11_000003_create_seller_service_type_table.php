<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_service_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('service_type_id')->constrained('service_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['seller_id', 'service_type_id']);
            $table->index('service_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_service_type');
    }
};