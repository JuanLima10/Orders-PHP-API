<?php

declare(strict_types=1);

return [
    'enable'        => true,
    'port'          => 9500,
    'json_dir'      => BASE_PATH . '/storage/swagger',
    'html'          => null,
    'url'           => '/swagger',
    'auto_generate' => true,
    'scan'          => [
        'paths' => [
            BASE_PATH . '/app',
        ],
    ],
    'processors' => [],
    'server'      => [
        'http' => [
            'servers' => [
                [
                    'url'         => 'http://127.0.0.1:9501',
                    'description' => 'Local Server',
                ],
            ],
            'info' => [
                'title'       => 'Hyperf Orders API',
                'description' => 'This is an Orders api with Hyperf, MySQL, Redis and MongoDB',
                'version'     => '1.0.0',
            ],
        ],
    ],
];
