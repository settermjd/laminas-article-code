<?php

namespace User\Service;

use League\OAuth2\Client\Provider\LinkedIn;
use Psr\Container\ContainerInterface;

class LinkedInClientFactory
{
    public function __invoke(ContainerInterface $container): LinkedIn
    {
        $config = $container->get('config')['linkedin'];
        return new LinkedIn([
            'clientId'          => $config['client_id'],
            'clientSecret'      => $config['client_secret'],
            'redirectUri'       => $config['redirect_url'],
        ]);
    }
}