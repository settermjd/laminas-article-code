<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;
use User\Handler\ForgotPasswordHandler;
use User\Handler\HomePageHandler;
use User\Handler\LinkedInIntegrationHandler;
use User\Handler\LoginHandler;
use User\Handler\LogoutHandler;
use User\Handler\RegistrationHandler;
use User\Handler\ResetPasswordHandler;
use User\Handler\UserProfileHandler;
use User\Middleware\UrlBuilderMiddleware;

/**
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', User\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', User\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', User\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', User\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', User\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', User\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', User\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     User\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get(
        '/[{id:\d+}]',
        [
            AuthenticationMiddleware::class,
            HomePageHandler::class
        ],
        'home'
    );
    $app->get(
        '/profile',
        [
            AuthenticationMiddleware::class,
            UserProfileHandler::class,
        ],
        'user.profile'
    );
    $app->route(
        '/login',
        LoginHandler::class,
        ['get', 'post'],
        'user.login'
    );
    $app->route(
        '/linkedin-integration',
        LinkedInIntegrationHandler::class,
        ['get', 'post'],
        'user.linkedin.integration'
    );
    $app->route(
        '/forgot-password',
        [
            UrlBuilderMiddleware::class,
            ForgotPasswordHandler::class,
        ],
        ['get', 'post'],
        'user.forgot-password'
    );
    $app->route(
        '/reset-password/{id:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}}',
        ResetPasswordHandler::class,
        ['get', 'post'],
        'user.reset-password'
    );
    $app->route(
        '/register',
        RegistrationHandler::class,
        ['get', 'post'],
        'user.register'
    );
    $app->get(
        '/logout',
        LogoutHandler::class,
        'user.logout'
    );
};
