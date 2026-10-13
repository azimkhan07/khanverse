<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $existing = DB::table('menu_items')->where('panel', 'admin')->where('title', 'Locations')->exists();

        if (!$existing) {
            DB::table('menu_items')->insert([
                'panel' => 'admin',
                'title' => 'Locations',
                'section' => 'Marketplace',
                'path' => '/locations',
                'icon' => 'locations',
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('menu_items')->where('panel', 'admin')->where('title', 'Locations')->delete();
    }
};
