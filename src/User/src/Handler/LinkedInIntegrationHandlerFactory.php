<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use League\OAuth2\Client\Provider\LinkedIn;
use Psr\Container\ContainerInterface;

class LinkedInIntegrationHandlerFactory
{
    public function __invoke(ContainerInterface $container) : LinkedInIntegrationHandler
    {
        return new LinkedInIntegrationHandler(
            $container->get(LinkedIn::class),
            $container->get(UsersTableGateway::class)
        );
    }
}
