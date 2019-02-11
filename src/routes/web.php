<?php

Route::group([
    'middleware' => array_merge(['web'], config('app_settings.middleware', []))
], function () {
    Route::get(config('app_settings.url'), config('app_settings.controller').'@index');
    Route::post(config('app_settings.url'), config('app_settings.controller').'@store');
});
