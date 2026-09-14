<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('city');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_source')->nullable()->after('longitude');
            $table->timestamp('location_updated_at')->nullable()->after('location_source');
        });

        Schema::table('login_histories', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('ip_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_source', 'location_updated_at']);
        });

        Schema::table('login_histories', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};