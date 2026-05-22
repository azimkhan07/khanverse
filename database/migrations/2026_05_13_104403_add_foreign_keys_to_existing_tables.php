<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToExistingTables extends Migration
{
    public function up()
    {

        Schema::table('orders', function (Blueprint $table) {

            $table->unsignedBigInteger('buyer_id')->nullable()->after('id');

            $table->unsignedBigInteger('seller_id')->nullable()->after('buyer_id');

            $table->unsignedBigInteger('service_id')->nullable()->after('seller_id');

            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');

            $table->foreign('seller_id')
                ->references('id')
                ->on('sellers')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');
        });

        Schema::table('reviews', function (Blueprint $table) {

            $table->unsignedBigInteger('order_id')->nullable();

            $table->unsignedBigInteger('buyer_id')->nullable();

            $table->unsignedBigInteger('seller_id')->nullable();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');

            $table->foreign('seller_id')
                ->references('id')
                ->on('sellers')
                ->onDelete('cascade');
        });


        Schema::table('projects', function (Blueprint $table) {

            $table->unsignedBigInteger('buyer_id')->nullable();

            $table->foreign('buyer_id')
                ->references('id')
                ->on('buyers')
                ->onDelete('cascade');
        });


        Schema::table('transactions', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable();

            $table->unsignedBigInteger('order_id')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });


        Schema::table('wallets', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });


        Schema::table('service_images', function (Blueprint $table) {

            $table->unsignedBigInteger('service_id')->nullable();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');
        });


        Schema::table('services', function (Blueprint $table) {

            $table->unsignedBigInteger('seller_id')->nullable();

            $table->unsignedBigInteger('category_id')->nullable();

            $table->foreign('seller_id')
                ->references('id')
                ->on('sellers')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });


        Schema::table('chat_messages', function (Blueprint $table) {

            // $table->unsignedBigInteger('conversation_id')->nullable();

            // $table->unsignedBigInteger('sender_id')->nullable();

            $table->foreign('conversation_id')
                ->references('id')
                ->on('chat_conversations')
                ->onDelete('cascade');

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        //
    }
}