<?php

declare(strict_types=1);

namespace User;

use User\EmailTransportFactory;
use User\Command\LinkedIn\PublishToLinkedInCommand;
use User\Command\LinkedIn\PublishToLinkedInCommandFactory;
use User\Database\ScheduledPostsTableGateway;
use User\Database\ScheduledPostsTableGatewayFactory;
use User\Database\UsersTableGateway;
use User\Database\UsersTableGatewayFactory;
use User\Middleware\TemplateDefaultsMiddleware;
use User\Middleware\TemplateDefaultsMiddlewareFactory;
use User\Middleware\UrlBuilderMiddleware;
use User\Middleware\UrlBuilderMiddlewareFactory;
use User\Service\Email\UserNotificationService;
use User\Service\Email\UserNotificationServiceFactory;
use User\Service\LinkedInClientFactory;
use Laminas\Mail\Transport\TransportInterface;
use League\OAuth2\Client\Provider\LinkedIn;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserRepository\PdoDatabase;
use Mezzio\Authentication\UserRepositoryInterface;

/**
 * The configuration provider for the User module
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
                Handler\ForgotPasswordHandler::class => Handler\ForgotPasswordHandlerFactory::class,
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                Handler\LinkedInIntegrationHandler::class => Handler\LinkedInIntegrationHandlerFactory::class,
                Handler\LoginHandler::class => Handler\LoginHandlerFactory::class,
                Handler\LogoutHandler::class => Handler\LogoutHandlerFactory::class,
                Handler\PostProcessorHandler::class => Handler\PostProcessorHandlerFactory::class,
                Handler\RegistrationHandler::class => Handler\RegistrationHandlerFactory::class,
                Handler\ResetPasswordHandler::class => Handler\ResetPasswordHandlerFactory::class,
                Handler\UserProfileHandler::class => Handler\UserProfileHandlerFactory::class,
                LinkedIn::class => LinkedInClientFactory::class,
                Middleware\IsLoggedInMiddleware::class => Middleware\IsLoggedInMiddlewareFactory::class,
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
