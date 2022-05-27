<?php

return [
    'linkedin' => [
        'client_id' => $_SERVER['LINKEDIN_CLIENT_ID'] ?? '',
        'client_secret' => $_SERVER['LINKEDIN_CLIENT_SECRET'] ?? '',
        'redirect_url' => $_SERVER['LINKEDIN_REDIRECT_URL'] ?? '',
    ]
];
