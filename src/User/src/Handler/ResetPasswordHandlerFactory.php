<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use User\Service\Email\UserNotificationService;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ResetPasswordHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ResetPasswordHandler
    {
        return new ResetPasswordHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UsersTableGateway::class),
            $container->get(UserNotificationService::class),
            $container->get(RouterInterface::class),
            $container->get(ServerUrlHelper::class),
        );
    }
}
