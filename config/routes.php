<?php

declare(strict_types=1);

use App\Middleware\IsLoggedInMiddleware;
use Mezzio\Application;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get(
        '/[{id:\d+}]',
        [
            IsLoggedInMiddleware::class,
            App\Handler\HomePageHandler::class
        ],
        'home'
    );
    $app->post(
        '/posts',
        [
            IsLoggedInMiddleware::class,
            App\Handler\PostProcessorHandler::class
        ],
        'posts.process'
    );
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    $app->get(
        '/profile',
        [
            IsLoggedInMiddleware::class,
            App\Handler\UserProfileHandler::class,
        ],
        'user.profile'
    );
    $app->route(
        '/login',
        App\Handler\LoginHandler::class,
        ['get', 'post'],
        'user.login'
    );
    $app->route(
        '/linkedin-integration',
        App\Handler\LinkedInIntegrationHandler::class,
        ['get', 'post'],
        'user.linkedin.integration'
    );
    $app->route(
        '/forgot-password',
        App\Handler\ForgotPasswordHandler::class,
        ['get', 'post'],
        'user.forgot-password'
    );
    $app->route(
        '/reset-password/{id:[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}}',
        App\Handler\ResetPasswordHandler::class,
        ['get', 'post'],
        'user.reset-password'
    );
    $app->route(
        '/register',
        App\Handler\RegistrationHandler::class,
        ['get', 'post'],
        'user.register'
    );
    $app->get(
        '/logout',
        App\Handler\LogoutHandler::class,
        'user.logout'
    );
};
