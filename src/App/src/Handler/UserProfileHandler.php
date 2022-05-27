<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use App\Entity\User;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class UserProfileHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $table
    ){}

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        /** @var User $user */
        $user = $this
            ->table
            ->select(
                [
                    'id' => $session->get('user_id')
                ]
            )
            ->current();

        return new HtmlResponse($this->renderer->render(
            'app::user-profile',
            [
                'user' => $user,
            ]
        ));
    }
}
