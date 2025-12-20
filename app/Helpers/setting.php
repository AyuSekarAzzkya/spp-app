<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting($key, $default = null) {
        $data = Setting::where('key', $key)->first();
        return $data ? $data->value : $default;
    }
}
