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

        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // bind storage into container
        $this->app->bind(
            'QCod\AppSettings\Setting\SettingStorage',
            'QCod\AppSettings\Setting\SettingEloquentStorage'
        );

        // bind app settings
        $this->app->singleton('app-settings', function ($app) {
            return new AppSettings($app->make('QCod\AppSettings\Setting\SettingStorage'));
        });
    }
}
