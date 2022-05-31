<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use User\Service\Email\UserNotificationService;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ForgotPasswordHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ForgotPasswordHandler
    {
        return new ForgotPasswordHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UsersTableGateway::class),
            $container->get(UserNotificationService::class),
        );
    }
}
