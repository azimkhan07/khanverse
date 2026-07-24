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
        if (Schema::hasTable('seller_certificates')) {
            return;
        }

        Schema::create('seller_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('title');
            $table->string('organization');
            $table->date('issue_date')->nullable();
            $table->string('certificate_file')->nullable();
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
        Schema::dropIfExists('seller_certificates');
    }
};
