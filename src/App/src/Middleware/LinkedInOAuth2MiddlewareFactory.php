<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

class LinkedInOAuth2MiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : LinkedInOAuth2Middleware
    {
        return new LinkedInOAuth2Middleware();
    }
}
