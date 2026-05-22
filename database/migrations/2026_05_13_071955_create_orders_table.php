<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            // $table->foreignId('buyer_id')
            //     ->constrained('buyers')
            //     ->onDelete('cascade');

            // $table->foreignId('seller_id')
            //     ->constrained('sellers')
            //     ->onDelete('cascade');

            // $table->foreignId('service_id')
            //     ->constrained()
            //     ->onDelete('cascade');

            $table->decimal('amount', 12, 2);

            $table->decimal('platform_fee', 12, 2)
                ->default(0);

            $table->enum('status', [
                'pending',
                'active',
                'delivered',
                'completed',
                'cancelled',
                'disputed'
            ])->default('pending');

            $table->longText('requirements')->nullable();

            $table->date('delivery_date')->nullable();

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
        Schema::dropIfExists('orders');
    }
}
