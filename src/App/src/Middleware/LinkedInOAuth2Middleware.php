<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use LinkedIn\Client;
use LinkedIn\Exception;
use LinkedIn\Scope;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LinkedInOAuth2Middleware implements MiddlewareInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if (empty($request->getQueryParams()['code'])) {
            $scopes = [
                Scope::READ_BASIC_PROFILE,
                Scope::READ_EMAIL_ADDRESS,
                Scope::SHARING,
            ];
            $session->set('state', $this->client->getState());
            $session->set('redirect_url', $this->client->getRedirectUrl());

            return new RedirectResponse($this->client->getLoginUrl($scopes));
        }


        $code = $request->getQueryParams()['code'];
        $state = $request->getQueryParams()['state'];
        if (empty($state) || $state !== $session->get('state')) {
            return new JsonResponse('Invalid state!');
        }

        try {
            $this->client->setRedirectUrl($session->get('redirect_url'));
            $session->set('token', $this->client->getAccessToken($code));
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new RedirectResponse(sprintf('/?code=%s', $code));
    }
}
