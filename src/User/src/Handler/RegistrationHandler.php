<?php

declare(strict_types=1);

namespace User\Handler;

use User\Database\UsersTableGateway;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripNewlines;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class RegistrationHandler implements RequestHandlerInterface
{
    private InputFilter $inputFilter;

    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly UsersTableGateway $userService
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

        $passwordInput = new Input('password');
        $firstNameInput = new Input('first_name');
        $lastNameInput = new Input('last_name');

        $this->inputFilter = new InputFilter();
        $this->inputFilter
            ->add($emailInput)
            ->add($passwordInput)
            ->add($firstNameInput)
            ->add($lastNameInput);
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if (strtoupper($request->getMethod()) === 'GET') {
            return new HtmlResponse($this->renderer->render('app::registration'));
        }

        /** @var ?FlashMessagesInterface $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

        $this->inputFilter->setData($request->getParsedBody());
        if (! $this->inputFilter->isValid()) {
            $flashMessages->flash(
                "message",
                "One or more of the fields is invalid",
            );
            return new RedirectResponse('/register');
        }

        $result = $this->userService->createUser($this->inputFilter->getValues());
        if ($result) {
            $user = $this->userService->findByEmail($this->inputFilter->getValue('email_address'));
            /** @var ?SessionInterface $session */
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $session->set('user_id', $user->getId());
        }

        return new RedirectResponse('/');
    }
}
