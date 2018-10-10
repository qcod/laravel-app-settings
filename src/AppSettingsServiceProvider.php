<?php

namespace QCod\AppSettings;

use Illuminate\Support\ServiceProvider;
use QCod\AppSettings\Setting\AppSettings;

class AppSettingsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/app_settings.php',
            'app_settings'
        );

        $this->loadViewsFrom(
            __DIR__ . '/resources/views',
            'app_settings'
        );

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/app_settings')
        ], 'views');

        $this->publishes([
            __DIR__ . '/config/app_settings.php' => config_path('app_settings.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // register setting service provider
        $this->app->register('QCod\Settings\SettingsServiceProvider');

        // bind app settings
        $this->app->singleton('app-settings', function ($app) {
            return new AppSettings($app->make('QCod\Settings\Setting\SettingStorage'));
        });
    }
}
