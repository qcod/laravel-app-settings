<?php

namespace QCod\AppSettings\Setting;

use Illuminate\Support\Collection;

interface SettingStorage
{

    /**
     * Get all settings from storage as key value pair
     *
     * @param bool $fresh ignore cached
     * @return Collection
     */
    public function all($fresh = false);

    /**
     * Get a setting from storage by key
     *
     * @param string $key
     * @param null $default
     * @param bool $fresh
     * @return mixed
     */
    public function get($key, $default = null, $fresh = false);

    /**
     * Check if setting with key exists
     *
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * Save a setting in storage
     *
     * @param $key string|array
     * @param $val string|mixed
     * @return mixed
     */
    public function set($key, $val = null);

    /**
     * Remove a setting from storage
     *
     * @param $key
     * @return mixed
     */
    public function remove($key);

    /**
     * Flush setting cache
     *
     * @return bool
     */
    public function flushCache();
}
