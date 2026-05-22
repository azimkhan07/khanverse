<?php

if (!function_exists('getSellerSidebar')) {
    function getSellerSidebar()
    {
        return [
            [
                'title' => 'Dashboard',
                'route' => 'seller.dashboard',
                'icon' => 'feather icon-home',
            ],
            [
                'title' => 'My Products',
                'route' => 'seller.products',
                'icon' => 'feather icon-box',
            ],
            [
                'title' => 'Orders',
                'route' => 'seller.orders',
                'icon' => 'feather icon-shopping-cart',
            ],
        ];
    }
}