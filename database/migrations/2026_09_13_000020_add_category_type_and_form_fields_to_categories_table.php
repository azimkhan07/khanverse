<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('category_type', ['technical', 'field'])->default('technical')->after('status');
            $table->json('form_fields')->nullable()->after('category_type');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['category_type', 'form_fields']);
        });
    }
};