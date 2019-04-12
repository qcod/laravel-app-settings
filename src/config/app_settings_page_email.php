<?php

return [
    'email' => [
        'title' => 'Email Settings',
        'descriptions' => 'How app email will be sent.',
        'icon' => 'fa fa-envelope',

        'inputs' => [
            [
                'name' => 'from_email',
                'type' => 'email',
                'view' => 'email',          // the name of the view, if left out, the type is the name of the view
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
];