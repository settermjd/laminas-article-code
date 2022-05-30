<?php

return [
    'authentication' => [
        'redirect' => '/login',
        'username' => 'email_address',
        'password' => 'password',
        'pdo' => [
            'dsn' => 'sqlite:' . __DIR__ . '/../../data/database/db.sqlite3',
            'table' => 'users',
            'field' => [
                'identity' => 'email_address',
                'password' => 'password',
            ],
        ],
    ],
];