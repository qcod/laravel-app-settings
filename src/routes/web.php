<?php

Route::group([
    'namespace' => 'QCod\AppSettings\Controllers',
    'middleware' => array_merge(['web'], config('app_settings.middleware', []))
], function () {
    Route::get(config('app_settings.url'), 'AppSettingController@index');

    Route::post(config('app_settings.url'), 'AppSettingController@store');
});
