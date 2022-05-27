<?php

namespace App;

use Laminas\Mail\Transport\Sendmail;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\TransportInterface;
use Psr\Container\ContainerInterface;

class EmailTransportFactory
{
    public function __invoke(ContainerInterface $container): TransportInterface
    {
        $config = $container->get('config')['email'];
        return match($config['transport']['type']) {
            'smtp' => new Smtp(new SmtpOptions($config['transport']['options'])),
            default => new Sendmail(),
        };
    }
}