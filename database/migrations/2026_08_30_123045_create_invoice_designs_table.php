<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_designs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['buyer', 'seller'])->default('buyer');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('logo_text')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('footer_line')->nullable();
            $table->string('about_line')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_designs');
    }
};
