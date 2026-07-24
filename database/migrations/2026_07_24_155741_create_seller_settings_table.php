<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('seller_settings')) {
            return;
        }

        Schema::create('seller_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->boolean('email_notification')->default(true);
            $table->boolean('push_notification')->default(true);
            $table->boolean('sms_notification')->default(false);
            $table->boolean('profile_visibility')->default(true);
            $table->boolean('dark_mode')->default(false);
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('language')->default('en');
            $table->timestamps();
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_settings');
    }
};
