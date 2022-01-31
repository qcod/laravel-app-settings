<?php

Route::group([
    'middleware' => array_merge(['web'], config('app_settings.middleware', []))
], function () {
    Route::get(config('app_settings.url'), config('app_settings.controller').'@index')->name(config('app_settings.route_names.index'));
    Route::post(config('app_settings.url'), config('app_settings.controller').'@store')->name(config('app_settings.route_names.store'));
});
