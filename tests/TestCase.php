<?php

namespace QCod\AppSettings\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
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
        return ['QCod\AppSettings\AppSettingsServiceProvider'];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'AppSettings' => 'QCod\AppSettings\Facade'
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
