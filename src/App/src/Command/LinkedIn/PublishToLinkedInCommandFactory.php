<?php

declare(strict_types=1);

namespace App\Command\LinkedIn;

use App\Database\ScheduledPostsTableGateway;
use League\OAuth2\Client\Provider\LinkedIn;
use Psr\Container\ContainerInterface;

class PublishToLinkedInCommandFactory
{
    public function __invoke(ContainerInterface $container) : PublishToLinkedInCommand
    {
        return new PublishToLinkedInCommand(
            $container->get(LinkedIn::class),
            $container->get(ScheduledPostsTableGateway::class),
        );
    }
}
