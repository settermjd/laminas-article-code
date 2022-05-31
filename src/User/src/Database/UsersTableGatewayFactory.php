<?php

namespace User\Database;

use Laminas\Db\Adapter\Adapter;
use Psr\Container\ContainerInterface;

class UsersTableGatewayFactory
{
    public function __invoke(ContainerInterface $container) : UsersTableGateway
    {
        return new UsersTableGateway($container->get(Adapter::class));
    }
}