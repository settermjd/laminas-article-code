<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use Laminas\Db\Adapter\Adapter;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container) : LoginHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $table = $container->get(UsersTableGateway::class);

        return new LoginHandler($renderer, $table);
    }
}
