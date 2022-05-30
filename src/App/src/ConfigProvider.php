<?php

declare(strict_types=1);

namespace App;

use App\Command\LinkedIn\PublishToLinkedInCommand;
use App\Command\LinkedIn\PublishToLinkedInCommandFactory;
use App\Database\ScheduledPostsTableGateway;
use App\Database\ScheduledPostsTableGatewayFactory;
use App\Database\UsersTableGateway;
use App\Database\UsersTableGatewayFactory;
use App\Middleware\TemplateDefaultsMiddleware;
use App\Middleware\TemplateDefaultsMiddlewareFactory;
use App\Middleware\UrlBuilderMiddleware;
use App\Middleware\UrlBuilderMiddlewareFactory;
use App\Service\Email\UserNotificationService;
use App\Service\Email\UserNotificationServiceFactory;
use App\Service\LinkedInClientFactory;
use Laminas\Mail\Transport\TransportInterface;
use League\OAuth2\Client\Provider\LinkedIn;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserRepository\PdoDatabase;
use Mezzio\Authentication\UserRepositoryInterface;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'laminas-cli' => $this->getCliConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                AuthenticationInterface::class => PhpSession::class,
                UserRepositoryInterface::class => PdoDatabase::class
            ],
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories'  => [
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                LinkedIn::class => LinkedInClientFactory::class,
                PublishToLinkedInCommand::class => PublishToLinkedInCommandFactory::class,
                ScheduledPostsTableGateway::class => ScheduledPostsTableGatewayFactory::class,
                TemplateDefaultsMiddleware::class => TemplateDefaultsMiddlewareFactory::class,
                TransportInterface::class => EmailTransportFactory::class,
                UrlBuilderMiddleware::class => UrlBuilderMiddlewareFactory::class,
                UserNotificationService::class => UserNotificationServiceFactory::class,
                UsersTableGateway::class => UsersTableGatewayFactory::class,
            ],
        ];
    }

    /**
     * Returns the CLI commands
     *
     * @return string[][]
     */
    public function getCliConfig() : array
    {
        return [
            'commands' => [
                'linkedin:publish-post' => PublishToLinkedInCommand::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
