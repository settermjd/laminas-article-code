<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use User\Service\Email\UserNotificationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\{StringTrim,StripNewlines,StripTags};
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
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
        private readonly UsersTableGateway $userService,
        private readonly UserNotificationService $userNotificationService
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

        /** @var ?FlashMessagesInterface $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

        if (strtoupper($request->getMethod()) === 'POST') {
            $formData = $request->getParsedBody();
            $this->inputFilter->setData($formData);

            if (! $this->inputFilter->isValid()) {
                $flashMessages->flash(
                    "message",
                    "Email address was invalid"
                );
                return new RedirectResponse(
                    $this->generateUri($request, 'user.forgot-password')
                );
            }

            $user = $this->userService->findByEmail($formData['email_address']);
            if ($user !== null) {
                $uuid = Uuid::uuid4();
                $this->userService->addForgotPasswordFlag(
                    $user->emailAddress,
                    $uuid->toString()
                );
                $this->userNotificationService->sendResetPasswordEmail(
                    $user,
                    $this->generateUri(
                        $request,
                        'user.reset-password',
                        ['id' => $uuid->toString()],
                    )
                );
                $flashMessages->flashNow(
                    "message",
                    "A reset password email has been sent to the registered email address."
                );
                return new RedirectResponse(
                    $this->generateUri($request, 'user.forgot-password')
                );
            }
        }

        $data['message'] = $flashMessages->getFlash("message");

        return new HtmlResponse($this->renderer->render('app::forgot-password', $data));
    }

    protected function generateUri(
        ServerRequestInterface $request,
        string $routeName,
        array $substitutions = [],
        array $options = []
    ): string
    {
        /** @var ?ServerUrlHelper $urlHelper */
        $urlHelper = $request->getAttribute(ServerUrlHelper::class);

        /** @var ?RouterInterface $router */
        $router = $request->getAttribute(RouterInterface::class);

        return $urlHelper->generate(
            $router->generateUri($routeName, $substitutions, $options)
        );
    }
}
