<?php

declare(strict_types=1);

namespace App\Handler;

use App\Database\UsersTableGateway;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripNewlines;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\StringLength;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserInterface;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly AuthenticationInterface $adapter
    ) {
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
        $passwordInput
            ->getValidatorChain()
            ->attach(new StringLength([
                'min' => 5,
                'max' => 64,
            ]));
        $passwordInput
            ->getFilterChain()
            ->attach(new StripTags())
            ->attach(new StripNewlines())
            ->attach(new StringTrim());

        $this->inputFilter = new InputFilter();
        $this->inputFilter
            ->add($emailInput)
            ->add($passwordInput);
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $data = [];

        /** @var ?FlashMessagesInterface $flashMessages */
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

        if (strtoupper($request->getMethod()) === 'POST') {
            $this->inputFilter->setData($request->getParsedBody());
            if (! $this->inputFilter->isValid()) {
                $flashMessages->flash(
                    "message",
                    sprintf(
                        "Either the email address or password was invalid. Reason: %s",
                        var_export($this->inputFilter->getMessages(), TRUE)
                    )
                );
                return new RedirectResponse('/login');
            }

            /** @var ?SessionInterface $session */
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $session->unset(UserInterface::class);
            if ($this->adapter->authenticate($request)) {
                return new RedirectResponse('/');
            }
        }

        $data['message'] = $flashMessages->getFlash("message");
        return new HtmlResponse($this->renderer->render(
            'app::login',
            $data
        ));
    }
}
