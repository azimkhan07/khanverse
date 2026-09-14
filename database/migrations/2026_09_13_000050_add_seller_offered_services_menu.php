<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $existing = DB::table('menu_items')->where('panel', 'seller')->where('title', 'Offered Services')->exists();

        if (!$existing) {
            DB::table('menu_items')->where('panel', 'seller')->where('title', 'Orders')->update(['sort_order' => 3]);
            DB::table('menu_items')->where('panel', 'seller')->where('title', 'Projects')->update(['sort_order' => 4]);

            DB::table('menu_items')->insert([
                'panel' => 'seller',
                'title' => 'Offered Services',
                'section' => 'Main',
                'path' => '/services/offered',
                'icon' => 'service-types',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('menu_items')->where('panel', 'seller')->where('title', 'Offered Services')->delete();
    }
};