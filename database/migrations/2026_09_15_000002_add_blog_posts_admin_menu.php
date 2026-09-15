<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $existing = DB::table('menu_items')->where('panel', 'admin')->where('title', 'Blog Posts')->exists();

        if (!$existing) {
            DB::table('menu_items')->insert([
                'panel' => 'admin',
                'title' => 'Blog Posts',
                'section' => 'Website',
                'path' => '/website/blog-posts',
                'icon' => 'blog-posts',
                'sort_order' => 10,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('menu_items')->where('panel', 'admin')->where('title', 'Blog Posts')->delete();
    }
};