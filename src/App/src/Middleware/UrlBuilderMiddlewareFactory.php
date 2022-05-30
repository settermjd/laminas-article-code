<?php

declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class UrlBuilderMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : UrlBuilderMiddleware
    {
        return new UrlBuilderMiddleware(
            $container->get(RouterInterface::class),
            $container->get(ServerUrlHelper::class)
        );
    }
}
