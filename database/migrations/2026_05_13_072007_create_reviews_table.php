<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {

            $table->id();

            // $table->foreignId('order_id')
            //     ->constrained()
            //     ->onDelete('cascade');

            // $table->foreignId('buyer_id')
            //     ->constrained('buyers')
            //     ->onDelete('cascade');

            // $table->foreignId('seller_id')
            //     ->constrained('sellers')
            //     ->onDelete('cascade');

            $table->tinyInteger('rating');

            $table->text('review')->nullable();

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
        Schema::dropIfExists('reviews');
    }
}
