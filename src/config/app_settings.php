<?php

return [

    // All the sections for the settings page
    'sections' => [
        'branding' => require(__DIR__ . '/app_settings_page_branding.php'),
        'email' => require(__DIR__ . '/app_settings_page_email.php'),
    ],

    // Setting page url, will be used for get and post request
    'url' => '/settings',
    'default_page' => 'email',

    // Any middleware you want to run on above route
    'middleware' => [],

    // View settings
    'setting_page_view' => 'app_settings::settings_page',
    'flash_partial' => 'app_settings::_flash',

    // Setting section class setting
    'section_class' => 'card mb-3',
    'section_heading_class' => 'card-header',
    'section_body_class' => 'card-body',

    // Input wrapper and group class setting
    'input_wrapper_class' => 'form-group',
    'input_class' => 'form-control',
    'input_error_class' => 'has-error',
    'input_invalid_class' => 'is-invalid',
    'input_hint_class' => 'form-text text-muted',
    'input_error_feedback_class' => 'text-danger',

    // Submit button
    'submit_btn_text' => 'Save Settings',
    'submit_success_message' => 'Settings has been saved.',

    // Remove any setting which declaration removed later from sections
    'remove_abandoned_settings' => false,

    // when the setting is not found, can we try to get the config setting for this key
    'allow_passthrough_config' => true,

    // Should we throw and exception if we cannot find the setting after we checked the config files
    // only in addition to 'allow_passthrough_config'
    'exception_on_nodefined_config' => true,

    // Controller to show and handle save setting
    'controller' => '\QCod\AppSettings\Controllers\AppSettingController',

    // settings group
    'setting_group' => function() {
        // return 'user_'.auth()->id();
        return 'default';
    }
];
