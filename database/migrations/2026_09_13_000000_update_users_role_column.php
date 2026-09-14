<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUsersRoleColumn extends Migration
{
    /**
     * Convert the users.role enum to a flexible string so the neutral
     * `user` role can be stored alongside admin/buyer/seller.
     */
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'user'");

        $indexes = collect(DB::select('SHOW INDEX FROM users'))->pluck('Key_name')->unique();

        if (! $indexes->contains('users_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
            });
        }
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'buyer', 'seller') NOT NULL DEFAULT 'buyer'");
    }
}