<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $adminMenus = [
            ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route_name' => 'admin.dashboard', 'roles' => ['admin'], 'sort_order' => 1, 'is_active' => true],
            ['title' => 'Users', 'icon' => 'fas fa-users', 'route_name' => null, 'roles' => ['admin'], 'sort_order' => 2, 'is_active' => true],
            ['title' => 'Orders', 'icon' => 'fas fa-shopping-cart', 'route_name' => 'admin.orders.index', 'roles' => ['admin'], 'sort_order' => 3, 'is_active' => true],
            ['title' => 'Projects', 'icon' => 'fas fa-project-diagram', 'route_name' => 'admin.projects.index', 'roles' => ['admin'], 'sort_order' => 4, 'is_active' => true],
            ['title' => 'Services', 'icon' => 'fas fa-concierge-bell', 'route_name' => 'admin.services.index', 'roles' => ['admin'], 'sort_order' => 5, 'is_active' => true],
            ['title' => 'Categories', 'icon' => 'fas fa-tags', 'route_name' => 'admin.categories.index', 'roles' => ['admin'], 'sort_order' => 6, 'is_active' => true],
            ['title' => 'Settings', 'icon' => 'fas fa-cog', 'route_name' => null, 'roles' => ['admin'], 'sort_order' => 7, 'is_active' => true],
            ['title' => 'Website', 'icon' => 'fas fa-globe', 'route_name' => null, 'roles' => ['admin'], 'sort_order' => 8, 'is_active' => true],
            ['title' => 'Roles', 'icon' => 'fas fa-user-shield', 'route_name' => 'admin.roles.index', 'roles' => ['admin'], 'sort_order' => 9, 'is_active' => true],
            ['title' => 'Modules', 'icon' => 'fas fa-puzzle-piece', 'route_name' => 'admin.modules.index', 'roles' => ['admin'], 'sort_order' => 10, 'is_active' => true],
        ];

        foreach ($adminMenus as $menuData) {
            MenuItem::firstOrCreate(
                ['route_name' => $menuData['route_name'], 'title' => $menuData['title']],
                $menuData
            );
        }

        $adminUsersMenu = MenuItem::where('title', 'Users')->first();
        if ($adminUsersMenu) {
            $buyerChildren = [
                ['title' => 'Buyers', 'icon' => 'fas fa-user', 'route_name' => 'admin.users.buyers.index', 'parent_id' => $adminUsersMenu->id, 'roles' => ['admin'], 'sort_order' => 1, 'is_active' => true],
                ['title' => 'Sellers', 'icon' => 'fas fa-user-tie', 'route_name' => 'admin.users.sellers.index', 'parent_id' => $adminUsersMenu->id, 'roles' => ['admin'], 'sort_order' => 2, 'is_active' => true],
            ];
            foreach ($buyerChildren as $child) {
                MenuItem::firstOrCreate(
                    ['route_name' => $child['route_name']],
                    $child
                );
            }
        }

        $adminSettingsMenu = MenuItem::where('title', 'Settings')->first();
        if ($adminSettingsMenu) {
            $settingChildren = [
                ['title' => 'Admin Settings', 'icon' => 'fas fa-cog', 'route_name' => 'admin.settings.admin', 'parent_id' => $adminSettingsMenu->id, 'roles' => ['admin'], 'sort_order' => 1, 'is_active' => true],
                ['title' => 'Seller Settings', 'icon' => 'fas fa-cog', 'route_name' => 'admin.settings.seller', 'parent_id' => $adminSettingsMenu->id, 'roles' => ['admin'], 'sort_order' => 2, 'is_active' => true],
                ['title' => 'Buyer Settings', 'icon' => 'fas fa-cog', 'route_name' => 'admin.settings.buyer', 'parent_id' => $adminSettingsMenu->id, 'roles' => ['admin'], 'sort_order' => 3, 'is_active' => true],
                ['title' => 'Frontend Settings', 'icon' => 'fas fa-cog', 'route_name' => 'admin.settings.frontend', 'parent_id' => $adminSettingsMenu->id, 'roles' => ['admin'], 'sort_order' => 4, 'is_active' => true],
                ['title' => 'Auth Settings', 'icon' => 'fas fa-cog', 'route_name' => 'admin.settings.auth', 'parent_id' => $adminSettingsMenu->id, 'roles' => ['admin'], 'sort_order' => 5, 'is_active' => true],
            ];
            foreach ($settingChildren as $child) {
                MenuItem::firstOrCreate(
                    ['route_name' => $child['route_name']],
                    $child
                );
            }
        }

        $adminWebsiteMenu = MenuItem::where('title', 'Website')->first();
        if ($adminWebsiteMenu) {
            $websiteChildren = [
                ['title' => 'Homepage', 'icon' => 'fas fa-home', 'route_name' => 'admin.website.homepage.index', 'parent_id' => $adminWebsiteMenu->id, 'roles' => ['admin'], 'sort_order' => 1, 'is_active' => true],
                ['title' => 'About', 'icon' => 'fas fa-info-circle', 'route_name' => 'admin.website.about.index', 'parent_id' => $adminWebsiteMenu->id, 'roles' => ['admin'], 'sort_order' => 2, 'is_active' => true],
                ['title' => 'FAQs', 'icon' => 'fas fa-question-circle', 'route_name' => 'admin.website.faq.index', 'parent_id' => $adminWebsiteMenu->id, 'roles' => ['admin'], 'sort_order' => 3, 'is_active' => true],
                ['title' => 'Testimonials', 'icon' => 'fas fa-quote-right', 'route_name' => 'admin.website.testimonials.index', 'parent_id' => $adminWebsiteMenu->id, 'roles' => ['admin'], 'sort_order' => 4, 'is_active' => true],
                ['title' => 'Banners', 'icon' => 'fas fa-image', 'route_name' => 'admin.website.banners.index', 'parent_id' => $adminWebsiteMenu->id, 'roles' => ['admin'], 'sort_order' => 5, 'is_active' => true],
            ];
            foreach ($websiteChildren as $child) {
                MenuItem::firstOrCreate(
                    ['route_name' => $child['route_name']],
                    $child
                );
            }
        }

        $sellerMenus = [
            ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route_name' => 'seller.dashboard', 'roles' => ['seller'], 'sort_order' => 1, 'is_active' => true],
            ['title' => 'Orders', 'icon' => 'fas fa-shopping-cart', 'route_name' => 'seller.orders.index', 'roles' => ['seller'], 'sort_order' => 2, 'is_active' => true],
            ['title' => 'Projects', 'icon' => 'fas fa-project-diagram', 'route_name' => 'seller.projects.index', 'roles' => ['seller'], 'sort_order' => 3, 'is_active' => true],
            ['title' => 'Services', 'icon' => 'fas fa-concierge-bell', 'route_name' => 'seller.services.index', 'roles' => ['seller'], 'sort_order' => 4, 'is_active' => true],
            ['title' => 'Reviews', 'icon' => 'fas fa-star', 'route_name' => 'seller.reviews.index', 'roles' => ['seller'], 'sort_order' => 5, 'is_active' => true],
            ['title' => 'Wallet', 'icon' => 'fas fa-wallet', 'route_name' => 'seller.wallet.index', 'roles' => ['seller'], 'sort_order' => 6, 'is_active' => true],
            ['title' => 'Settings', 'icon' => 'fas fa-cog', 'route_name' => 'seller.settings.index', 'roles' => ['seller'], 'sort_order' => 7, 'is_active' => true],
        ];

        foreach ($sellerMenus as $menu) {
            MenuItem::firstOrCreate(
                ['route_name' => $menu['route_name']],
                $menu
            );
        }

        $buyerMenus = [
            ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route_name' => 'buyer.dashboard', 'roles' => ['buyer'], 'sort_order' => 1, 'is_active' => true],
            ['title' => 'Orders', 'icon' => 'fas fa-shopping-cart', 'route_name' => 'buyer.orders.index', 'roles' => ['buyer'], 'sort_order' => 2, 'is_active' => true],
            ['title' => 'Projects', 'icon' => 'fas fa-project-diagram', 'route_name' => 'buyer.projects.index', 'roles' => ['buyer'], 'sort_order' => 3, 'is_active' => true],
            ['title' => 'Reviews', 'icon' => 'fas fa-star', 'route_name' => 'buyer.reviews.index', 'roles' => ['buyer'], 'sort_order' => 4, 'is_active' => true],
            ['title' => 'Wallet', 'icon' => 'fas fa-wallet', 'route_name' => 'buyer.wallet.index', 'roles' => ['buyer'], 'sort_order' => 5, 'is_active' => true],
            ['title' => 'Settings', 'icon' => 'fas fa-cog', 'route_name' => 'buyer.settings.index', 'roles' => ['buyer'], 'sort_order' => 6, 'is_active' => true],
        ];

        foreach ($buyerMenus as $menu) {
            MenuItem::firstOrCreate(
                ['route_name' => $menu['route_name']],
                $menu
            );
        }
    }
}
