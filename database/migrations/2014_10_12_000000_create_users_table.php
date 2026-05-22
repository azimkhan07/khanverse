<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            $table->string('username')->unique();

            $table->string('email')->unique();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('phone')->unique()->nullable();

            $table->boolean('phone_verified')->default(false);

            $table->string('password');

            $table->enum('role', ['admin', 'buyer', 'seller'])
                ->default('buyer');

            $table->boolean('status')->default(true);

            $table->boolean('is_verified')->default(false);

            $table->boolean('is_banned')->default(false);

            $table->boolean('two_factor_enabled')->default(false);

            $table->timestamp('last_login_at')->nullable();

            $table->ipAddress('last_login_ip')->nullable();

            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
