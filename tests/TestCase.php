<?php

namespace QCod\AppSettings\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function assertArraySubset(array $subset, $array): void
    {
        if ($array instanceof \Illuminate\Support\Collection) {
            $array = $array->all();
        }

        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array);

            if (is_array($value)) {
                $this->assertArraySubset($value, $array[$key]);
            } else {
                $this->assertSame($value, $array[$key]);
            }
        }
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'QCod\AppSettings\AppSettingsServiceProvider',
            'QCod\Settings\SettingsServiceProvider',
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'AppSettings' => 'QCod\Settings\Facade'
        ];
    }

    /**
     * Set inputs on settings ui
     * @param $inputs
     */
    protected function configureInputs($inputs): void
    {
        config(['app_settings.sections' => [
            'app' => [
                'title' => 'General Settings',
                'descriptions' => 'Application general settings.',
                'icon' => 'fa fa-cog',

                'inputs' => $inputs
            ]
        ]
        ]);
    }
}
