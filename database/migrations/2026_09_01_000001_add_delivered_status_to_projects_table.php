<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeliveredStatusToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE projects MODIFY status ENUM('open', 'in_progress', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'open'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE projects MODIFY status ENUM('open', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'open'");
    }
}