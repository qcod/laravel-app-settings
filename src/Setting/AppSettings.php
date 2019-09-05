<?php

namespace QCod\AppSettings\Setting;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use QCod\Settings\Setting\SettingStorage;

class AppSettings
{
    /**
     * @var SettingStorage
     */
    private $settingStorage;

    /**
     * AppSettings constructor.
     *
     * @param SettingStorage $settingStorage
     */
    public function __construct(SettingStorage $settingStorage)
    {
        $groupName = $this->getSettingsGroupName();

        if( $groupName && is_callable($groupName) ) {
            $groupName = $this->runCallback($groupName, 'setting_group', null);
            $this->settingStorage = $settingStorage->group($groupName);
        } else {
            $this->settingStorage = $settingStorage;
        }
    }

    /**
     * Get al the settings from storage
     *
     * @param bool $fresh
     * @return \Illuminate\Support\Collection
     */
    public function all($fresh = false)
    {
        return $this->settingStorage->all($fresh);
    }

    /**
     * Get a setting and cast the value
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $settingField = $this->getSettingField($name);

        // get the setting and fallback to default value from config
        $value = $this->settingStorage->get(
            $name,
            Arr::get($settingField, 'value', $default)
        );

        // cast the value
        $outValue = $this->castValue(Arr::get($settingField, 'data_type'), $value, true);

        // check for accessor to run
        if ($accessor = Arr::get($settingField, 'accessor')) {
            $outValue = $this->runCallback($accessor, $name, $value);
        }

        return $outValue;
    }

    /**
     * Save a setting into storage
     *
     * @param $name string|array
     * @param $value string|mixed
     * @return mixed
     */
    public function set($name, $value)
    {
        $settingField = $this->getSettingField($name);
        $dataType = Arr::get($settingField, 'data_type');

        $val = $this->castValue($dataType, $value);

        // check for mutator to run
        if ($mutator = Arr::get($settingField, 'mutator')) {
            $val = $this->runCallback($mutator, $name, $value);
        }

        return $this->settingStorage->set($name, $val);
    }

    /**
     * Remove a setting from storage
     *
     * @param $name
     * @return mixed
     */
    public function remove($name)
    {
        return $this->settingStorage->remove($name);
    }

    /**
     * Save incoming settings
     *
     * @param $request \Illuminate\Http\Request
     */
    public function save($request)
    {
        // get all defined settings from config
        $allDefinedSettings = $this->getAllSettingFields();

        // set all the fields with updated values
        $allDefinedSettings->each(function ($setting) use ($request) {
            $settingName = $setting['name'];
            $type = $setting['type'];

            // handle file upload
            if (in_array($type, ['file', 'image']) && !isset($setting['mutator'])) {
                $this->uploadFile($setting, $request);
                // any other type of field
            } elseif ($request->has($settingName) || $type == 'checkbox') {
                $this->set($settingName, $request->get($settingName));
            }
        });

        // clean up any abandoned settings
        $this->cleanUpSettings($allDefinedSettings);
    }

    /**
     * Load and resolve closers in config
     *
     * @param $config
     * @return array
     */
    public function loadConfig($config)
    {
        // resolve any callback for inputs
        array_walk_recursive($config, function (&$value, $key) {
            if (!in_array($key, ['mutator', 'accessor', 'rules']) && is_callable($value)) {
                // skip any string which dont look like namesapce
                if (is_string($value) && Str::contains($value, '\\') == false) {
                    return;
                }

                $value = $this->runCallback($value, $key, '');
            }
        });

        return $config;
    }

    /**
     * Get the settings UI sections as collection
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getSettingUISections()
    {
        return collect(config('app_settings.sections', []));
    }

    /**
     * Get all the setting fields defined from all sections
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllSettingFields()
    {
        return $this->getSettingUISections()->flatMap(function ($field) {
            return Arr::get($field, 'inputs', []);
        });
    }

    /**
     * Get a single setting field config
     *
     * @param $name
     * @return array
     */
    public function getSettingField($name)
    {
        return $this->getAllSettingFields()
            ->first(function ($field) use ($name) {
                return Arr::get($field, 'name') == $name;
            }, []);
    }

    /**
     * Build validation rules for laravel validator
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->getAllSettingFields()
            ->pluck('rules', 'name')
            ->reject(function ($val) {
                return is_null($val);
            })
            ->toArray();
    }

    /**
     * Cast a value into a type
     *
     * @param $dataType
     * @param $value
     * @param bool $out
     * @return bool|int|mixed|string
     */
    private function castValue($dataType, $value, $out = false)
    {
        switch ($dataType) {
            case 'array':
                return $this->castToArray($value, $out);
                break;

            case 'int':
            case 'integer':
            case 'number':
                return intval($value);
                break;

            case 'boolean':
            case 'bool':
                return boolval($value);
                break;

            default:
                return $value;
        }
    }

    /**
     * Run a callback to mutate and access the value
     *
     * @param $callback
     * @param $name
     * @param $value
     * @return string|mixed
     */
    protected function runCallback($callback, $name, $value)
    {
        if (is_callable($callback)) {
            $value = $callback($value, $name);
        }

        // try callback handler class
        if (is_string($callback)) {
            $instance = app($callback);
            $value = $instance->handle($value, $name);
        }

        return $value;
    }

    /**
     * Remove abandoned settings
     *
     * @param $allDefinedSettings \Illuminate\Support\Collection
     */
    protected function cleanUpSettings($allDefinedSettings)
    {
        if (!config('app_settings.remove_abandoned_settings')) {
            return;
        }

        $this->settingStorage->all()->keys()
            ->diff($allDefinedSettings->pluck('name'))
            ->each(function ($field) {
                $this->remove($field);
            });
    }

    /**
     * Cast value to array
     *
     * @param $value
     * @param $out
     * @return array|mixed|string
     */
    private function castToArray($value, $out)
    {
        if ($out) {
            return empty($value) ? [] : json_decode($value, true);
        }

        return json_encode($value);
    }

    /**
     * Upload a file
     *
     * @param $setting array
     * @param $request Request
     * @return string|null
     */
    private function uploadFile($setting, $request)
    {
        $settingName = Arr::get($setting, 'name');

        // get the disk and path to upload
        $disk = Arr::get($setting, 'disk', 'public');
        $path = Arr::get($setting, 'path', '/');

        $uploadedPath = null;
        $oldFile = $this->get($settingName);

        if ($request->hasFile($settingName)) {
            $uploadedPath = $request->$settingName->store($path, $disk);
            $this->set($settingName, $uploadedPath);

            // delete old file
            $this->deleteFile($oldFile, $disk);

            return $uploadedPath;
        }

        // check for remove asked
        if ($request->has('remove_file_' . $settingName)) {
            $this->deleteFile($oldFile, $disk);
            $this->set($settingName, null);
        }

        return $uploadedPath;
    }

    /**
     * Delete a file
     *
     * @param $oldFile
     * @param $disk
     */
    private function deleteFile($oldFile, $disk): void
    {
        if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
            Storage::disk($disk)->delete($oldFile);
        }
    }

    /**
     * Get the name of settings group
     *
     * @return string
     */
    protected function getSettingsGroupName()
    {
        return config('app_settings.setting_group');
    }
}
