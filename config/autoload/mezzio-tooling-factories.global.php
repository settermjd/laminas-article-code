<?php

/**
 * This file generated by Mezzio\Tooling\Factory\ConfigInjector.
 *
 * Modifications should be kept at a minimum, and restricted to adding or
 * removing factory definitions; other dependency types may be overwritten
 * when regenerating this file via mezzio-tooling commands.
 */
 
declare(strict_types=1);

return [
    'dependencies' => [
        'factories' => [
            App\Command\LinkedIn\PublishToLinkedInCommand::class => App\Command\LinkedIn\PublishToLinkedInCommandFactory::class,
            App\Handler\ForgotPasswordHandler::class => App\Handler\ForgotPasswordHandlerFactory::class,
            App\Handler\LinkedInIntegrationHandler::class => App\Handler\LinkedInIntegrationHandlerFactory::class,
            App\Handler\LoginHandler::class => App\Handler\LoginHandlerFactory::class,
            App\Handler\LogoutHandler::class => App\Handler\LogoutHandlerFactory::class,
            App\Handler\PostProcessorHandler::class => App\Handler\PostProcessorHandlerFactory::class,
            App\Handler\RegistrationHandler::class => App\Handler\RegistrationHandlerFactory::class,
            App\Handler\ResetPasswordHandler::class => App\Handler\ResetPasswordHandlerFactory::class,
            App\Handler\UserProfileHandler::class => App\Handler\UserProfileHandlerFactory::class,
            App\Middleware\IsLoggedInMiddleware::class => App\Middleware\IsLoggedInMiddlewareFactory::class,
            App\Middleware\TemplateDefaultsMiddleware::class => App\Middleware\TemplateDefaultsMiddlewareFactory::class,
            App\Service\Email\UserNotificationService::class => App\Service\Email\UserNotificationServiceFactory::class,
        ],
    ],
];
