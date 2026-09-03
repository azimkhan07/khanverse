<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Fixes the stray, unused `attribute` column on orders that had no default,
 * which broke every Order::create() with "Field 'attribute' doesn't have a
 * default value". Making it nullable is safe as nothing writes to it.
 */
return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE orders MODIFY `attribute` VARCHAR(255) NULL DEFAULT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE orders MODIFY `attribute` VARCHAR(255) NOT NULL');
    }
};
