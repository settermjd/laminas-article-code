<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class RegistrationHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationHandler
    {
        return new RegistrationHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UsersTableGateway::class)
        );
    }
}
