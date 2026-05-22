<?php

use App\Models\Setting;

if (!function_exists('setting')) {

    function setting($group, $key, $default = null)
    {
        static $cache = [];

        $cacheKey = $group . '.' . $key;

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $value = Setting::where('group', $group)
            ->where('key', $key)
            ->value('value');

        $cache[$cacheKey] = $value ?? $default;

        return $cache[$cacheKey];
    }
}