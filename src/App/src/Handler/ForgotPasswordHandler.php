<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use App\Service\Email\UserNotificationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripNewlines;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Ramsey\Uuid\Uuid;

class ForgotPasswordHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $table,
        private readonly UserNotificationService $userNotificationService,
        private readonly UrlHelper $urlHelper,
    ){
        $emailInput = new Input('email_address');
        $emailInput
            ->getValidatorChain()
            ->attach(new EmailAddress());
        $emailInput
            ->getFilterChain()
            ->attach(new StripTags())
            ->attach(new StripNewlines())
            ->attach(new StringTrim());

        $this->inputFilter = new InputFilter();
        $this->inputFilter->add($emailInput);
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $data = [];

        /** @var FlashMessagesInterface $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            $this->inputFilter->setData($formData);

            if (! $this->inputFilter->isValid()) {
                $flashMessages->flash(
                    "message",
                    "Email address was invalid"
                );
                return new RedirectResponse(
                    $this->urlHelper->generate('user.forgot-password')
                );
            }

            $user = $this->table->findByEmail($formData['email_address']);
            if ($user !== null) {
                $uuid = Uuid::uuid4();
                $this->table->addForgotPasswordFlag($user->getEmailAddress(), $uuid->toString());
                $emailUrl = $this
                    ->urlHelper
                    ->generate(
                        'user.reset-password',
                        [
                            'id' => $uuid->toString(),
                        ]
                    );
                $this->userNotificationService->sendResetPasswordEmail($user, $emailUrl);
                $flashMessages->flashNow(
                    "message",
                    "Reset password has been sent to the registered email address"
                );
                return new RedirectResponse(
                    $this->urlHelper->generate('user.forgot-password')
                );
            }
        }

        $data['message'] = $flashMessages->getFlash("message");

        return new HtmlResponse($this->renderer->render('app::forgot-password', $data));
    }
}
