<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class RegistrationHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $table
    ){}

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() === 'GET') {
            return new HtmlResponse($this->renderer->render(
                'app::registration',
                []
            ));
        }

        $formData = $request->getParsedBody();
        $result = $this->table->createUser($formData);
        if ($result) {
            $user = $this->table->findByEmail($formData['email_address']);
            /** @var SessionInterface $session */
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $session->set('user_id', $user->getId());
        }

        return new RedirectResponse('/');
    }
}
