<?php

return [

    'model' => [
        'label' => 'Exception',
        'plural_label' => 'Exceptions',
    ],

    'navigation' => [
        'label' => 'Exception',
        'group' => 'Settings',
        'icon' => 'heroicon-o-chip',
    ],

    'pills' => [

        'exception' => [
            'label' => 'Exception',
            'icon' => 'heroicon-o-chip',
        ],

        'headers' => [
            'label' => 'Headers',
            'icon' => 'heroicon-o-switch-horizontal',
        ],

        'cookies' => [
            'label' => 'Cookies',
            'icon' => 'heroicon-o-database',
        ],

        'body' => [
            'label' => 'Body',
            'icon' => 'heroicon-s-code',
        ],

        'queries' => [
            'label' => 'Queries',
            'icon' => 'heroicon-s-database',
        ],
    ],

    'columns' => [
        'method' => 'Method',
        'path' => 'Path',
        'type' => 'Type',
        'code' => 'Code',
        'ip' => 'IP',
        'occurred_at' => 'Occurred at',
    ],

];
