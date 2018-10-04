<?php

if (!function_exists('setting')) {

    /**
     * Get app setting stored in db
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app()->make('app-settings');
        }

        if (is_array($key)) {
            return app()->make('app-settings')->set($key);
        }

        return app()->make('app-settings')->get($key, value($default));
    }
}
