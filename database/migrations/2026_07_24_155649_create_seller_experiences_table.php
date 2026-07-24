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
        if (Schema::hasTable('seller_experiences')) {
            return;
        }

        Schema::create('seller_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('designation');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('currently_working')->default(false);
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('seller_experiences');
    }
};
