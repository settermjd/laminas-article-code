<?php

declare(strict_types=1);

namespace App\Service\Email;

use Laminas\Mail\Transport\TransportInterface;
use Psr\Container\ContainerInterface;

class UserNotificationServiceFactory
{
    public function __invoke(ContainerInterface $container) : UserNotificationService
    {
        return new UserNotificationService(
            $container->get(TransportInterface::class),
            $container->get('config')['email']
        );
    }
}
