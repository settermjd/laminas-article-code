<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use App\Entity\User;
use Mezzio\Authentication\UserInterface;
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
        private readonly UsersTableGateway $userService
    ){}

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var ?SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        /** @var ?UserInterface $user */
        $user = $session->get(UserInterface::class);

        /** @var ?User $user */
        $user = $this
            ->userService
            ->findByEmail($user['username']);

        return new HtmlResponse($this->renderer->render(
            'app::user-profile',
            [
                'user' => $user,
            ]
        ));
    }
}
