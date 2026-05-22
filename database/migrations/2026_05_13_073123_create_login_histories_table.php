<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->ipAddress('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->string('browser')->nullable();

            $table->string('device')->nullable();

            $table->string('platform')->nullable();

            $table->timestamp('login_at')->nullable();

            $table->timestamp('logout_at')->nullable();

            $table->boolean('is_successful')->default(true);

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
        Schema::dropIfExists('login_histories');
    }
}
