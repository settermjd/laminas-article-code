<?php

namespace App\Database;

use Laminas\Db\Adapter\Adapter;
use Psr\Container\ContainerInterface;

class ScheduledPostsTableGatewayFactory
{
    public function __invoke(ContainerInterface $container) : ScheduledPostsTableGateway
    {
        return new ScheduledPostsTableGateway($container->get(Adapter::class));
    }
}