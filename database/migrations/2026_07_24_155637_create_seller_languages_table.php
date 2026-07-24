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
        if (Schema::hasTable('seller_languages')) {
            return;
        }

        Schema::create('seller_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('language');
            $table->enum('level', ['basic', 'intermediate', 'advanced', 'native'])->default('basic');
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
        Schema::dropIfExists('seller_languages');
    }
};
