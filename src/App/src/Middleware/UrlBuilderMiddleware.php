<?php

declare(strict_types=1);

namespace App\Middleware;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UrlBuilderMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ServerUrlHelper $urlHelper
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $request = $request->withAttribute(RouterInterface::class, $this->router);
        $request = $request->withAttribute(ServerUrlHelper::class, $this->urlHelper);

        return $handler->handle($request);
    }
}