<?php

declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class IsLoggedInMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : IsLoggedInMiddleware
    {
        $router = $container->get(RouterInterface::class);

        return new IsLoggedInMiddleware($router);
    }
}
