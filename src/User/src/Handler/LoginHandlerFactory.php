<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use Laminas\Db\Adapter\Adapter;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container) : LoginHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $table = $container->get(AuthenticationInterface::class);

        return new LoginHandler($renderer, $table);
    }
}
