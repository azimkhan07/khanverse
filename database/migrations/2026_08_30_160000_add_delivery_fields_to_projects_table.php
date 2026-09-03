<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('delivery_method', 20)->default('digital')->after('status');
            $table->string('delivery_key', 32)->nullable()->after('delivery_method');
            $table->string('delivery_key_hash', 64)->nullable()->after('delivery_key');
            $table->timestamp('delivery_key_shown_at')->nullable()->after('delivery_key_hash');
            $table->timestamp('delivery_key_verified_at')->nullable()->after('delivery_key_shown_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_method', 'delivery_key', 'delivery_key_hash',
                'delivery_key_shown_at', 'delivery_key_verified_at',
            ]);
        });
    }
};