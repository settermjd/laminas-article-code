<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use User\Service\Email\UserNotificationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\{StringTrim,StripNewlines,StripTags};
use Laminas\InputFilter\{Input,InputFilter};
use Laminas\Validator\Identical;
use Mezzio\Flash\{FlashMessageMiddleware,FlashMessagesInterface};
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\{ResponseInterface,ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class ResetPasswordHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $userService,
        private readonly UserNotificationService $userNotificationService,
        private readonly RouterInterface $router,
        private readonly ServerUrlHelper $serverUrlHelper,
    ){
        $password = new Input('password');
        $password
            ->getFilterChain()
            ->attach(new StripTags())
            ->attach(new StripNewlines())
            ->attach(new StringTrim());
        $confirmPassword = new Input('confirm_password');
        $confirmPassword
            ->getValidatorChain()
            ->attach(new Identical([
                'token' => 'password'
            ]));
        $confirmPassword
            ->getFilterChain()
            ->attach(new StripTags())
            ->attach(new StripNewlines())
            ->attach(new StringTrim());

        $this->inputFilter = new InputFilter();
        $this->inputFilter
            ->add($password)
            ->add($confirmPassword);
    }

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
            $flashMessages->flash(
                "message",
                "No reset password request is available for that user."
            );
            return new RedirectResponse('/');
        }

        if (strtoupper($request->getMethod()) === 'POST') {
            $this->inputFilter->setData($request->getParsedBody());
            if (! $this->inputFilter->isValid()) {
                $flashMessages->flash(
                    "message",
                    "The passwords do not match",
                );
                return new RedirectResponse('/reset-password/' . $resetId);
            }

            $emailUrl = $this->serverUrlHelper->generate($this->router->generateUri('home'));
            $this->userService->resetPassword(
                $user->emailAddress,
                $this->inputFilter->getValue('password')
            );
            $this->userNotificationService
                ->sendResetPasswordConfirmationEmail(
                    $user->emailAddress,
                    $user->fullName,
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
