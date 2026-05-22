<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {

            $table->id();

            // $table->foreignId('seller_id')
            //     ->constrained('sellers')
            //     ->onDelete('cascade');

            // $table->foreignId('category_id')
            //     ->constrained()
            //     ->onDelete('cascade');

            $table->string('title');

            $table->string('slug')->unique();

            $table->longText('description');

            $table->decimal('price', 12, 2);

            $table->integer('delivery_days');

            $table->integer('revisions')->default(0);

            $table->string('thumbnail')->nullable();

            $table->enum('status', [
                'draft',
                'active',
                'paused'
            ])->default('draft');

            $table->bigInteger('views')->default(0);

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
        Schema::dropIfExists('services');
    }
}
