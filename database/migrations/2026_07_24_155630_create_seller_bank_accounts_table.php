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
        if (Schema::hasTable('seller_bank_accounts')) {
            return;
        }

        Schema::create('seller_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('upi_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('seller_bank_accounts');
    }
};
