<?php 

if (!function_exists('getBuyerSidebar')) {
    function getBuyerSidebar()
    {
        return [
            [
                'title' => 'Dashboard',
                'route' => 'buyer.dashboard',
                'icon' => 'feather icon-home',
            ],
            [
                'title' => 'My Orders',
                'route' => 'buyer.orders',
                'icon' => 'feather icon-package',
            ],
            [
                'title' => 'Profile',
                'route' => 'buyer.profile',
                'icon' => 'feather icon-user',
            ],
        ];
    }
}