<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'fas fa-code', 'status' => true],
            ['name' => 'Mobile App Development', 'slug' => 'mobile-app-development', 'icon' => 'fas fa-mobile-alt', 'status' => true],
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design', 'icon' => 'fas fa-palette', 'status' => true],
            ['name' => 'Graphic Design', 'slug' => 'graphic-design', 'icon' => 'fas fa-paint-brush', 'status' => true],
            ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'icon' => 'fas fa-bullhorn', 'status' => true],
            ['name' => 'Content Writing', 'slug' => 'content-writing', 'icon' => 'fas fa-pen-fancy', 'status' => true],
            ['name' => 'SEO', 'slug' => 'seo', 'icon' => 'fas fa-search', 'status' => true],
            ['name' => 'Video Editing', 'slug' => 'video-editing', 'icon' => 'fas fa-video', 'status' => true],
            ['name' => 'Data Science', 'slug' => 'data-science', 'icon' => 'fas fa-chart-line', 'status' => true],
            ['name' => 'DevOps', 'slug' => 'devops', 'icon' => 'fas fa-server', 'status' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
