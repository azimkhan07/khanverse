<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->time('visit_start_time')->nullable()->after('delivery_method');
            $table->time('visit_end_time')->nullable()->after('visit_start_time');
            $table->json('working_days')->nullable()->after('visit_end_time');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['visit_start_time', 'visit_end_time', 'working_days']);
        });
    }
};