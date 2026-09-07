<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('requirements_title')->nullable()->after('requirements');
            $table->string('requirements_type')->nullable()->after('requirements_title');
            $table->string('requirements_docs')->nullable()->after('requirements_type');
            $table->string('decline_reason')->nullable()->after('delivery_date');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['requirements_title', 'requirements_type', 'requirements_docs', 'decline_reason']);
        });
    }
};
