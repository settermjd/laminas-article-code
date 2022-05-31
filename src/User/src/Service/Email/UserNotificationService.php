<?php

declare(strict_types=1);

namespace User\Service\Email;

use User\Entity\User;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;

class UserNotificationService
{
    public function __construct(
        private readonly TransportInterface $transport,
        private readonly array $config
    ){}

    /**
     * @todo Need to inject a reset password id and the reset password URL
     */
    public function sendResetPasswordEmail(User $user, string $resetPasswordUrl)
    {
        $body =<<<EOF
We got a request to change the password for the account with the email address: %s.

If you don't want to reset your password, you can ignore this email.

Open %s to reset your password.
EOF;

        $mail = new Message();
        $mail
            ->setBody(sprintf($body, $user->getEmailAddress(), $resetPasswordUrl))
            ->setFrom($this->config['fromAddress'], $this->config['fromName'])
            ->addTo($user->getEmailAddress(), $user->getFullName())
            ->setSubject("Reset your password");

        $this->transport->send($mail);
    }

    public function sendResetPasswordConfirmationEmail(string $toAddress, string $toName, string $baseUrl)
    {
        $body =<<<EOF
Your password has now been reset.

You can login here: %s.

Your awesome support team.
EOF;

        $mail = new Message();
        $mail
            ->setBody(sprintf($body, $baseUrl))
            ->setFrom($this->config['fromAddress'], $this->config['fromName'])
            ->addTo($toAddress, $toName)
            ->setSubject("Your password has been reset");

        $this->transport->send($mail);
    }
}