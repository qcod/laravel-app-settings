<?php

if (! function_exists('setting')) {

    /**
     * Get app setting stored in db.
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        $settings = app()->make('app-settings');

        if (is_null($key)) {
            return $settings;
        }

        if (is_array($key)) {
            return $settings->set($key);
        }

        return $settings->get($key, value($default));
    }
}
