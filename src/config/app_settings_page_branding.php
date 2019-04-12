<?php

return [
    'app' => [
        'title' => 'General Settings',
        'descriptions' => 'Application general settings.', // (optional)
        'icon' => 'fa fa-cog', // (optional)

        'inputs' => [
            [
                'name' => 'app.name', // unique key for setting
                'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                'view' => 'text', // the name of the view blade file , if left out, the type is the name of the view
                'label' => 'App Name', // label for input
                // optional properties
                'placeholder' => config('app.name'), // placeholder for input
                'class' => 'form-control', // override global input_class
                'style' => '', // any inline styles
                'rules' => 'required|min:2|max:20', // validation rules for this input
                'value' => config('app.name'),
                'hint' => 'You can set the app name here' // help block text for input
            ],
            [
                'name' => 'logo',
                'type' => 'image',
                'label' => 'Upload logo',
                'hint' => 'Must be an image and cropped in desired size',
                'rules' => 'image|max:500',
                'disk' => 'public', // which disk you want to upload
                'path' => 'app', // path on the disk,
                'preview_class' => 'thumbnail',
                'preview_style' => 'height:40px'
            ]
        ]
    ],
];