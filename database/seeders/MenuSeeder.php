<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $adminMenus = [
            ['section' => 'Overview', 'title' => 'Dashboard', 'path' => '/', 'icon' => 'dashboard', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Marketplace', 'title' => 'Orders', 'path' => '/orders', 'icon' => 'orders', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Marketplace', 'title' => 'Projects', 'path' => '/projects', 'icon' => 'projects', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Marketplace', 'title' => 'Services', 'path' => '/services', 'icon' => 'services', 'panel' => 'admin', 'sort_order' => 2],
            ['section' => 'Marketplace', 'title' => 'Categories', 'path' => '/categories', 'icon' => 'categories', 'panel' => 'admin', 'sort_order' => 3],
            ['section' => 'Users', 'title' => 'Buyers', 'path' => '/buyers', 'icon' => 'buyers', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Users', 'title' => 'Sellers', 'path' => '/sellers', 'icon' => 'sellers', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Users', 'title' => 'Login Devices', 'path' => '/devices', 'icon' => 'devices', 'panel' => 'admin', 'sort_order' => 2],
            ['section' => 'Users', 'title' => 'Suspicious', 'path' => '/suspicious', 'icon' => 'suspicious', 'panel' => 'admin', 'sort_order' => 3],
            ['section' => 'Users', 'title' => 'Login History', 'path' => '/login-history', 'icon' => 'login-history', 'panel' => 'admin', 'sort_order' => 4],
            ['section' => 'Access & System', 'title' => 'Roles', 'path' => '/roles', 'icon' => 'roles', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Access & System', 'title' => 'Permissions', 'path' => '/permissions', 'icon' => 'permissions', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Access & System', 'title' => 'Menus', 'path' => '/menus', 'icon' => 'menus', 'panel' => 'admin', 'sort_order' => 2],
            ['section' => 'Access & System', 'title' => 'Settings', 'path' => '/settings', 'icon' => 'settings', 'panel' => 'admin', 'sort_order' => 3],
            ['section' => 'Invoicing', 'title' => 'Invoice Designs', 'path' => '/invoices', 'icon' => 'invoices', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Invoicing', 'title' => 'Invoice Settings', 'path' => '/invoices/settings', 'icon' => 'invoice-settings', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Email', 'title' => 'Email Settings', 'path' => '/email-settings', 'icon' => 'email-settings', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Payments', 'title' => 'Payment Gateways', 'path' => '/payment-gateways', 'icon' => 'payment-gateways', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Payments', 'title' => 'Settlements', 'path' => '/settlements', 'icon' => 'settlements', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Website', 'title' => 'Banners', 'path' => '/website/banners', 'icon' => 'banners', 'panel' => 'admin', 'sort_order' => 0],
            ['section' => 'Website', 'title' => 'Homepage', 'path' => '/website/homepage', 'icon' => 'homepage', 'panel' => 'admin', 'sort_order' => 1],
            ['section' => 'Website', 'title' => 'Pages', 'path' => '/website/pages', 'icon' => 'website', 'panel' => 'admin', 'sort_order' => 2],
            ['section' => 'Website', 'title' => 'FAQs', 'path' => '/website/faqs', 'icon' => 'faqs', 'panel' => 'admin', 'sort_order' => 3],
            ['section' => 'Website', 'title' => 'Testimonials', 'path' => '/website/testimonials', 'icon' => 'testimonials', 'panel' => 'admin', 'sort_order' => 4],
            ['section' => 'Website', 'title' => 'SEO', 'path' => '/website/seo', 'icon' => 'seo', 'panel' => 'admin', 'sort_order' => 5],
            ['section' => 'Website', 'title' => 'Maintenance', 'path' => '/website/maintenance', 'icon' => 'maintenance', 'panel' => 'admin', 'sort_order' => 6],
            ['section' => 'Website', 'title' => 'Tutorial Videos', 'path' => '/tutorials', 'icon' => 'tutorials', 'panel' => 'admin', 'sort_order' => 7],
            ['section' => 'Website', 'title' => 'Brand Partners', 'path' => '/brand-partners', 'icon' => 'brand-partners', 'panel' => 'admin', 'sort_order' => 8],
            ['section' => 'Website', 'title' => 'Team Members', 'path' => '/team-members', 'icon' => 'team-members', 'panel' => 'admin', 'sort_order' => 9],
        ];

        foreach ($adminMenus as $menu) {
            MenuItem::updateOrCreate(
                ['title' => $menu['title'], 'panel' => $menu['panel']],
                array_merge($menu, ['is_active' => 1])
            );
        }

        $sellerMenus = [
            ['section' => 'Main', 'title' => 'Dashboard', 'path' => '/', 'icon' => 'dashboard', 'panel' => 'seller', 'sort_order' => 0],
            ['section' => 'Main', 'title' => 'Services', 'path' => '/services', 'icon' => 'services', 'panel' => 'seller', 'sort_order' => 1],
            ['section' => 'Main', 'title' => 'Orders', 'path' => '/orders', 'icon' => 'orders', 'panel' => 'seller', 'sort_order' => 2],
            ['section' => 'Main', 'title' => 'Projects', 'path' => '/projects', 'icon' => 'projects', 'panel' => 'seller', 'sort_order' => 3],
            ['section' => 'Finance', 'title' => 'Wallet', 'path' => '/wallet', 'icon' => 'wallet', 'panel' => 'seller', 'sort_order' => 0],
            ['section' => 'Finance', 'title' => 'Reviews', 'path' => '/reviews', 'icon' => 'reviews', 'panel' => 'seller', 'sort_order' => 1],
            ['section' => 'Account', 'title' => 'Settings', 'path' => '/settings', 'icon' => 'settings', 'panel' => 'seller', 'sort_order' => 0],
            ['section' => 'Account', 'title' => 'Notifications', 'path' => '/notifications', 'icon' => 'notifications', 'panel' => 'seller', 'sort_order' => 1],
        ];

        foreach ($sellerMenus as $menu) {
            MenuItem::updateOrCreate(
                ['title' => $menu['title'], 'panel' => $menu['panel']],
                array_merge($menu, ['is_active' => 1])
            );
        }

        $buyerMenus = [
            ['section' => 'Main', 'title' => 'Dashboard', 'path' => '/', 'icon' => 'dashboard', 'panel' => 'buyer', 'sort_order' => 0],
            ['section' => 'Main', 'title' => 'Orders', 'path' => '/orders', 'icon' => 'orders', 'panel' => 'buyer', 'sort_order' => 1],
            ['section' => 'Main', 'title' => 'Projects', 'path' => '/projects', 'icon' => 'projects', 'panel' => 'buyer', 'sort_order' => 2],
            ['section' => 'Finance', 'title' => 'Wallet', 'path' => '/wallet', 'icon' => 'wallet', 'panel' => 'buyer', 'sort_order' => 0],
            ['section' => 'Finance', 'title' => 'Reviews', 'path' => '/reviews', 'icon' => 'reviews', 'panel' => 'buyer', 'sort_order' => 1],
            ['section' => 'Account', 'title' => 'Settings', 'path' => '/settings', 'icon' => 'settings', 'panel' => 'buyer', 'sort_order' => 0],
            ['section' => 'Account', 'title' => 'Notifications', 'path' => '/notifications', 'icon' => 'notifications', 'panel' => 'buyer', 'sort_order' => 1],
            ['section' => 'Account', 'title' => 'Support', 'path' => '/support', 'icon' => 'support', 'panel' => 'buyer', 'sort_order' => 2],
        ];

        foreach ($buyerMenus as $menu) {
            MenuItem::updateOrCreate(
                ['title' => $menu['title'], 'panel' => $menu['panel']],
                array_merge($menu, ['is_active' => 1])
            );
        }
    }
}
