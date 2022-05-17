<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Db\Adapter\Adapter;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class PostProcessorHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PostProcessorHandler
    {
        $renderer = $container->get(TemplateRendererInterface::class);
        $database = $container->get(Adapter::class);

        return new PostProcessorHandler($renderer, $database);
    }
}
