<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('title');
            $table->string('icon')->nullable();

            // Route binding (Laravel named route)
            $table->string('route_name')->nullable();

            // Hierarchy (self join)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('menu_items')
                ->onDelete('cascade');

            // Role based access (admin/seller/buyer etc)
            $table->json('roles')->nullable();

            // Permission (optional Laravel Gate/Spatie)
            $table->string('permission')->nullable();

            // Ordering
            $table->integer('sort_order')->default(0);

            // Active/Inactive toggle
            $table->boolean('is_active')->default(1);

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
        Schema::dropIfExists('menu_items');
    }
}
