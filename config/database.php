<?php

declare(strict_types=1);

return [
    'default' => 'sqlite',

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],
];
