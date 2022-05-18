<?php

namespace App\Service;

use LinkedIn\Client;
use Psr\Container\ContainerInterface;

class LinkedInClientFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        $config = $container->get('config')['linkedin'];
        return (new Client(
            $config['client_id'],
            $config['client_secret']
        ))->setRedirectUrl($config['redirect_url']);
    }
}