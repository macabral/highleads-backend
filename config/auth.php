<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'usuarios',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'usuarios',
        ],
    ],

    'providers' => [
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Usuarios::class
        ]
    ]
];