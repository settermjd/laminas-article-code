<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UserProfileHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserProfileHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $table = $container->get(UsersTableGateway::class);
        return new UserProfileHandler($renderer, $table);
    }
}
