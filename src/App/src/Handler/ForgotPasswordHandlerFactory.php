<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use App\Service\Email\UserNotificationService;
use Mezzio\Helper\UrlHelper;
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
            $container->get(UrlHelper::class),
        );
    }
}
