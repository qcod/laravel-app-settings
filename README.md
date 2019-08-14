## Laravel App Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/qcod/laravel-app-settings.svg)](https://packagist.org/packages/qcod/laravel-app-settings)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/qcod/laravel-app-settings/master.svg)](https://travis-ci.org/qcod/laravel-app-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/qcod/laravel-app-settings.svg)](https://packagist.org/packages/qcod/laravel-app-settings)

Use `qcod/laravel-app-settings` to add settings manager with UI in your Laravel app. It stores settings in the database and by default uses Bootstrap 4 for styling but you can configure it to work with any css system.

> All the settings saved in db are cached to improve performance by reducing sql query to zero.

### Installation

**1** - You can install the package via composer:

```bash
$ composer require qcod/laravel-app-settings
```

**2** - If you are installing on Laravel 5.4 or lower you will be needed to manually register Service Provider by adding it in `config/app.php` providers array and Facade in aliases arrays.

```php
'providers' => [
    //...
    QCod\AppSettings\AppSettingsServiceProvider::class,
]

'aliases' => [
    //...
    "AppSettings" => QCod\AppSettings\Facade::class
]
```

In Laravel 5.5 or above the service provider automatically get registered and a facade `AppSettings::get('app_name')` will be available.

**3** - Now you should publish the config file with:

```bash
php artisan vendor:publish --provider="QCod\AppSettings\AppSettingsServiceProvider" --tag="config"
```

It will create [`config/app_settings.php`](#config-file) with all the configuration option and way to define your setting inputs divided into sections.

**4** - Now run the migration by `php artisan migrate` to create the settings table.

### Getting Started

First you need to define all the settings you want in our settings page. For example we will need these settings `app_name`, `from_email` & `from_name`. Lets define it in config:

**config/app_settings.php**

```php
<?php

[
    //...
    // All the sections for the settings page
    'sections' => [

        'app' => [
            'title' => 'General Settings',
            'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)

            'inputs' => [
                [
                    'name' => 'app_name', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'App Name', // label for input
                    // optional properties
                    'placeholder' => 'Application Name', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:2|max:20', // validation rules for this input
                    'value' => 'QCode', // any default value
                    'hint' => 'You can set the app name here' // help block text for input
                ]
            ]
        ],
        'email' => [
            'title' => 'Email Settings',
            'descriptions' => 'How app email will be sent.',
            'icon' => 'fa fa-email',

            'inputs' => [
                [
                    'name' => 'from_email',
                    'type' => 'email',
                    'label' => 'From Email',
                    'placeholder' => 'Application from email',
                    'rules' => 'required|email',
                ],
                [
                 'name' => 'from_name',
                 'type' => 'text',
                 'label' => 'Email from Name',
                 'placeholder' => 'Email from Name',
                ]
            ]
        ]
    ]
];
```

Now if you visit `http://yourapp.com/settings` route, you will get the UI with all the settings you defined.

![Laravel App Settings UI](app_settings_ui.jpg)

You can change the layout to fit it in with your app design by changing app_settings config:

```php
// View settings
'setting_page_view' => 'your_setting', // blade view path
```

Next open the `resources/views/your_setting.blade.php` and include settings partial where you want to display the settings:

```blade
@extends('layout')

@section('content')
    @include('app_settings::_settings')
@endsection
```

Now yo should be seeing settings page as part of your application with your layout 😎.

### Access saved settings

You have `setting('app_name', 'default value')` and Facade `AppSettings::get('app_name', 'default value')` which you can use to get the stored settings.

### Change the url for settings

If your app needs different url to access the settings page you can change from config:

```php
// Setting page url, will be used for get and post request
'url' => 'app-settings',
// http://yourapp.com/app-settings
```

### Use Group Setting

Many time you want to store settings in a group. With version `1.1` you can define a group name from your `config/app_settings.php`. You have a closer to return the name of group as string

```php
return [

    // All the sections for the settings page
    'sections' => [...]
    
    ...
    // settings group
    'setting_group' => function() {
        return 'user_'.auth()->id();
    }
```

In this case you can have different settings for each user.

### Use without UI

If you want to just store the key-value pair into DB and don't want the UI to manage settings for example in API? You should use [qcod/laravel-settings package](https://github.com/qcod/laravel-settings) instead. This package uses it under the hood to persist the settings.

If you want to use both UI and settings as key-value pair in DB alongside, for that simply use the helper function `setting()` or `AppSetting::get('app_name')` to store and retrieve settings from DB. For this, you don't need to define any section and inputs in `app_settings.php` config.

When using both UI and settings as a key-value pair, make sure to set `'remove_abandoned_settings' => false` in **config/app_settings.php** otherwise any undefined input fields will be removed when you save settings from UI.

Here are list of available methods:

```php
setting()->all($fresh = false);
setting()->get($key, $defautl = null);
setting()->set($key, $value);
setting()->has($key);
setting()->remove($key);
```

### Access setting in JavaScript

Many a times these settings needs be accessed from your JavaScript. One way you can do it by just sticking it in your layout blade template as global variable.

```blade
// layout.blade.php
<head>
<title>@yield('title', 'Settings')</title>
<script>
    window.App = {!! json_encode([
            'settings' => \setting()->all(),
            'anyOtherThings' => []
    ]); !!}
</script>
</head>
```

With this you will be able to access defined settings in your Vue Component or any JavaScript code.

```js
App.settings.app_name;

// or define a computed property on Component root level
const app = new Vue({
  el: "#app",
  computed: {
    myApp() {
      return window.App;
    }
  }
});

// access it like this
myApp.settings.app_name;
```

### Input types

Here are all the input types with attributes you can define, but you are free to add your [own custom input type](#custom-input-type) if needed.

> Every input must have a minimum of `name`, `type` & `label` attributes.

#### text, number, email

These are literally the same things with just type change and `min` and `max` attribute for number type.

```php
// text
[
    'name' => 'app_name',
    'type' => 'text',
    'label' => 'App Name',
    // optional fields
    'data_type' => 'string',
    'rules' => 'required|min:2|max:20',
    'placeholder' => 'Application Name',
    'class' => 'form-control',
    'style' => 'color:red',
    'value' => 'QCode',
    'hint' => 'You can set the app name here'
],

// number
[
    'name' => 'users_allowed',
    'type' => 'number',
    'label' => 'Number of users allowed',
    // optional fields
    'data_type' => 'int',
    'min' => 5,
    'max' => 100,
    'rules' => 'required|min:5|max:100',
    'placeholder' => 'Number of users allowed',
    'class' => 'form-control',
    'style' => 'color:red',
    'value' => 5,
    'hint' => 'You can set the number of users allowed to be added.'
]

// email
[
    'name' => 'from_email',
    'type' => 'email',
    'label' => 'From Email',
    // optional fields
    'rules' => 'required|email',
    'placeholder' => 'Emails will be sent from this address',
    'class' => 'form-control',
    'style' => 'color:red',
    'value' => 'noreply@example.com',
    'hint' => 'All the system generated email will be sent from this address.'
]
```

> 'data_type' can be used to cast the input data, it can be `array`, `int|integer|number`, `boolean|bool` and `string`.
> In case you need something else you always use [Accessor](#accessor) to do it.

#### textarea

A textarea field is same as text but it has `rows` and `cols` properties.

```php
[
    'type' => 'textarea',
    'name' => 'maintenance_note',
    'label' => 'Maintenance note',
    'rows' => 4,
    'cols' => 10,
    'placeholder' => 'What you want user to show when app is in maintenance mode.'
],
```

#### select

A select box can be defined with options:

```php
[
    'type' => 'select',
    'name' => 'date_format',
    'label' => 'Date format',
    'rules' => 'required',
    'options' => [
        'm/d/Y' => date('m/d/Y'),
        'm.d.y' => date("m.d.y"),
        'j, n, Y' => date("j, n, Y"),
        'M j, Y' => date("M j, Y"),
        'D, M j, Y' => date('D, M j, Y')
    ]
],
```

### Select options from database

You can also populate select options dynamically. In most cases it will be coming from the database, just use a closure for this:

```php
[
    'type' => 'select',
    'name' => 'city',
    'label' => 'City',
    'rules' => 'required',
    'options' => function() {
        return App\City::pluck('name', 'id')->toArray()
    }
],
```

> Note: You can use a closure (anonymous function) for most of the fields on inputs if you need to resolve field value dynamically.

#### boolean

Boolean is just a radio input group with yes or no option, you can also change it to select by setting `options` array:

```php
// as radio inputs
[
    'name' => 'maintenance_mode',
    'type' => 'boolean',
    'label' => 'Maintenance',
    'value' => false,
    'class' => 'w-auto',
    // optional fields
    'true_value' => 'on',
    'false_value' => 'off',
],
// as select options
[
    'name' => 'maintenance_mode',
    'type' => 'boolean',
    'label' => 'Maintenance',
    'value' => false,
    'class' => 'w-auto',
    // optional fields
    'options' => [
        '1' => 'Yes',
        '0' => 'No',
    ],
],
```

#### checkbox

Add a checkbox input

```php
[
    'type' => 'checkbox',
    'label' => 'Try Guessing user locals',
    'name' => 'guess_local',
    'value' => '1'
]
```

#### checkbox_group

Add a group of checkboxes

```php
[
    'type' => 'checkbox_group',
    'label' => 'Days to run scheduler',
    'name' => 'scheduler_days',
    'data_type' => 'array', // required
    'options' => [
        'Sunday', 'Monday', 'Tuesday'
    ]
]
```

#### image, file

You can use `image` and `file` input to upload files. File upload will be automatically handled and old files will be replaced with new.

If you define `mutator` on input then its up to you to handle the uploading, you must return the path of uploaded file from mutator to store in settings and deal with old files.

> . You don't need to use mutator in most of the cases.

You can define `rules` to validate incoming files, either it can be image or any document.

```php
[
    'name' => 'logo',
    'type' => 'image',
    'label' => 'Upload logo',
    'hint' => 'Must be an image and cropped in desired size',
    'rules' => 'image|max:500',
    'disk' => 'public', // which disk you want to upload, default to 'public'
    'path' => 'app', // path on the disk, default to '/',
    'preview_class' => 'thumbnail', // class for preview of uploaded image
    'preview_style' => 'height:40px' // style for preview
]

// handle uploads by yourself using `mutator`
[
    'name' => 'logo',
    'type' => 'image',
    'label' => 'Upload logo',
    'hint' => 'Must be an image and cropped in desired size',
    'rules' => 'image|max:500',

    // a simple mutator
    'mutator' => function($value, $key) {
        // handle image do some reszing etc
        $image = Intervention\Image::make(request()->file($key));

        $path = Storage::disk('public')->put(
            $imagePath,
            (string) $image->encode(null, $imageQuality),
            'public'
        );

        // delete old image etc
        Storage::disk('public')->delete(\setting($key));

        // finally return new path to be stored in db
        return $path;
    }
]
```

### Not using Bootstrap 4

If your app doesn't use Twitter Bootstrap 4 you can easily customize this in app_settings.php to follow your css library like Bulma, Foundatio CSS or any other custom solution:

```php
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
```

### Customizing app settings views

In some case if your app needs custom views you can publish app settings view and then you can customize every part of the setting fields.

```bash
php artisan vendor:publish --provider="QCod\AppSettings\AppSettingsServiceProvider" --tag="views"
```

### Custom input type

Although this package comes with all the inputs you will need. If you need something which not included you can just define an input in your app settings section and give it a custom type, how about a daterange field `type="daterange"`.
Next you will be required to publish the views and add a blade view inside `resources/views/vendor/app_settings/fields/` folder and match the name of the field like `daterange.blade.php`.

```blade
@component('app_settings::input_group', compact('field'))
<div class="row">
    <div class="col-md-6">
        <label>
            From
            <input
                type="date"
                name="from_{{ $field['name'] }}"
                class="{{ array_get( $field, 'class', config('app_settings.input_class', 'form-control')) }}"
                value="{{ old('from_'.$field['name'], array_get(\setting('from_'.$field['name']), 'from')) }}"
            >
        </label>
    </div>
    <div class="col-md-6">
        <label>
            To
            <input
                type="date"
                name="to_{{ $field['name'] }}"
                class="{{ array_get( $field, 'class', config('app_settings.input_class', 'form-control')) }}"
                value="{{ old('to_'.$field['name'], array_get(\setting($field['name']), 'to')) }}"
            >
        </label>
    </div>
</div>
@endcomponent
```

`@component('app_settings::input_group', compact('field'))` will add the `label` and `hint` with `error` feedback text.

To use this custom input you should define it `in app_settings.php` something like this:

```php
<?php
[
    'name' => 'registration_allowed',
    'type' => 'daterange',
    'label' => 'Registration Allowed',
    'hint' => 'A date range when registration is allowed',
    'mutator' => function($value, $key) {
        // combine both from_registration_allowed and to_registration_allowed
        $rangeValues = [
            'from' => request('from_registration_allowed'),
            'to' => request('to_registration_allowed'),
        ];

        return json_encode($rangeValues);
    },
    'accessor' => function($value, $key) {
        return is_null($value) ? ['from' => '', 'to' => ''] : json_decode($value, true);
    },
]
```

This should render your date range field. You can create any type of fields with this.

### Accessor and Mutator

Just like an Eloquent model It allows you to define accessor and mutator on inputs which comes handy when creating custom inputs.

#### Accessor

An accessor can change the setting value while its accessed, it could be a `Closer` or a class with `handle($value, $key)` method.

```php
<?php
// app settings input
[
    'name' => 'app_name',
    'type' => 'text',
    'accessor' => '\App\Accessors\AppNameAccessor'
];

// use a class
class AppNameAccessor {
    public function handle($value, $key) {
        return ucfirst($value);
    }
}

// or you can use Closer
[
    'name' => 'app_name',
    'type' => 'text',
    'accessor' => function($value, $key) {
        return ucfirst($value);
    }
];
```

#### Mutator

A Mutator can change the setting value before it stored in db, same as accessor it could be a `Closer` or a class with `handle($value, $key)` method.

```php
<?php
// app settings input
[
    'name' => 'app_name',
    'type' => 'text',
    'mutator' => '\App\Mutators\AppNameMutator'
];

// use a class
class AppNameMutator {
    public function handle($value, $key) {
        return ucfirst($value). ' Inc.';
    }
}

// or you can use Closer
[
    'name' => 'app_name',
    'type' => 'text',
    'mutator' => function($value, $key) {
        return ucfirst($value). ' Inc.';
    }
];
```

### Using different Controller
In order to use your controller to show and store settings you can do it by changing the `app_settings.controller`:

```php
// change the controller in config/app_settings.php at the bottom

// Controller to show and handle save setting
'controller' => '\App\Http\Controllers\SettingsController',
```

Make sure you have `index()` and `store(Request $request)` method in your controller.

```php
// Controller
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QCod\AppSettings\SavesSettings;
use App\Http\Controllers\Controller;

class SettingsController extends Controller 
{ 
    use SavesSettings;
    
    // you can override following methods from trait
    
    // to display the settings view
    public function index()
    {
       return 'I am settings page'.
    }
    
    // to store settings changes
    public function store(Request $request)
    {
       return $request->all().
    }
}
```

### Config file

```php
<?php
return [

    // All the sections for the settings page
    'sections' => [
        'app' => [
            'title' => 'General Settings',
            'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)

            'inputs' => [
                [
                    'name' => 'app_name', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'App Name', // label for input
                    // optional properties
                    'placeholder' => 'Application Name', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:2|max:20', // validation rules for this input
                    'value' => 'QCode', // any default value
                    'hint' => 'You can set the app name here' // help block text for input
                ]
            ]
        ],
        'email' => [
            'title' => 'Email Settings',
            'descriptions' => 'How app email will be sent.',
            'icon' => 'fa fa-envelope',

            'inputs' => [
                [
                    'name' => 'from_email',
                    'type' => 'email',
                    'label' => 'From Email',
                    'placeholder' => 'Application from email',
                    'rules' => 'required|email',
                ],
                [
                    'name' => 'from_name',
                    'type' => 'text',
                    'label' => 'Email from Name',
                    'placeholder' => 'Email from Name',
                ]
            ]
        ]
    ],

    // Setting page url, will be used for get and post request
    'url' => 'settings',

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

    // Controller to show and handle save setting
    'controller' => '\QCod\AppSettings\Controllers\AppSettingController'
    
    // settings group
    'setting_group' => function() {
        // return 'user_'.auth()->id();
        return 'default';
    }
];
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Testing

The package contains some integration/smoke tests, set up with Orchestra. The tests can be run via phpunit.

```bash
$ composer test
```

### Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email saquibweb@gmail.com instead of using the issue tracker.

### Credits

- [Mohd Saqueib Ansari](https://github.com/saqueib)

### About QCode.in

QCode.in (https://www.qcode.in) is blog by [Saqueib](https://github.com/saqueib) which covers All about Full Stack Web Development.

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
