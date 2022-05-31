<?php

declare(strict_types=1);

namespace User\Handler;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use League\OAuth2\Client\Provider\LinkedIn;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LinkedInIntegrationHandler implements RequestHandlerInterface
{
    private LinkedIn $client;
    private TableGatewayInterface $table;

    public function __construct(LinkedIn $client, TableGatewayInterface $table)
    {
        $this->client = $client;
        $this->table = $table;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if (empty($request->getQueryParams()['code'])) {
            $authUrl = $this->client->getAuthorizationUrl();
            $session->set('oauth2state', $this->client->getState());
            return new RedirectResponse($authUrl);
        }

        $code = $request->getQueryParams()['code'];
        $state = $request->getQueryParams()['state'];
        if (empty($_GET['state']) || ($_GET['state'] !== $state)) {
            $session->unset('oauth2state');
            return new JsonResponse('Invalid state!');
        }

        // Try to get an access token (using the authorization code grant)
        $token = $this->client->getAccessToken(
            'authorization_code',
            [
                'code' => $code
            ]
        );

        // Optional: Now you have a token you can look up a users profile data
        try {
            /** @var LinkedInResourceOwner $user */
            $user = $this->client->getResourceOwner($token);
            $userId = $user->getId();
            if ($userId !== null) {
                $this->table->update(
                    ['linkedin_urn' => $userId,],
                    ['id' => $session->get('user_id'),]
                );

                return new RedirectResponse('/profile');
            }
        } catch (\Exception $e) {
            // Failed to get user details
            exit('Oh dear...');
        }
    }
}
