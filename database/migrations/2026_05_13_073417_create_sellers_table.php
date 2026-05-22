<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('full_name');

            $table->string('profile_image')->nullable();

            $table->longText('bio')->nullable();

            $table->text('skills')->nullable();

            $table->string('country')->nullable();

            $table->string('city')->nullable();

            $table->decimal('hourly_rate', 10, 2)->nullable();

            $table->enum('experience_level', [
                'junior',
                'mid',
                'senior'
            ])->default('junior');

            $table->decimal('total_earning', 12, 2)
                ->default(0);

            $table->boolean('available_for_work')
                ->default(true);

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
        Schema::dropIfExists('sellers');
    }
}
