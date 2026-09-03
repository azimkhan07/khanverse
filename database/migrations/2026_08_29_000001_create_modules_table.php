<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('route')->nullable();
            $table->foreignId('menu_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('view_path')->nullable();
            $table->string('controller')->nullable();
            $table->string('panel')->default('admin');
            $table->json('roles')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
