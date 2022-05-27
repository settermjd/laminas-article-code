<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use App\Service\Email\UserNotificationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class ResetPasswordHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $userService,
        private readonly UserNotificationService $userNotificationService,
        private readonly RouterInterface $router,
        private readonly ServerUrlHelper $serverUrlHelper,
    ){}

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $resetId = $request->getAttribute('id');
        $data = [
            'reset_id' => $resetId,
        ];

        /** @var FlashMessagesInterface $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

        $user = $this->userService->findByResetPasswordId($resetId);
        if ($user === null) {
            $flashMessages->flash("message", "No reset password request is available for that user.");
            return new RedirectResponse('/');
        }

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            $emailUrl = $this->serverUrlHelper->generate($this->router->generateUri('home'));
            $this->userService->resetPassword($user->getEmailAddress(), $formData['password']);
            $this->userNotificationService
                ->sendResetPasswordConfirmationEmail(
                    $user->getEmailAddress(),
                    $user->getFullName(),
                    $emailUrl,
                );
            $flashMessages->flash("message", "Password has been reset.");
            return new RedirectResponse('/');
        }

        return new HtmlResponse($this->renderer->render(
            'app::reset-password',
            $data
        ));
    }
}
