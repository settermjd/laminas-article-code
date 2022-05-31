<?php

namespace User\Handler;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

trait UriHelperTrait
{
    protected function generateUri(
        ServerRequestInterface $request,
        string $routeName,
        array $substitutions = [],
        array $options = []
    ): string
    {
        /** @var ?ServerUrlHelper $urlHelper */
        $urlHelper = $request->getAttribute(ServerUrlHelper::class);

        /** @var ?RouterInterface $router */
        $router = $request->getAttribute(RouterInterface::class);

        return $urlHelper->generate(
            $router->generateUri($routeName, $substitutions, $options)
        );
    }
}