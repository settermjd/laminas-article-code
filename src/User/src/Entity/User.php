<?php
declare(strict_types=1);

namespace User\Entity;

class User
{
    private readonly int $id;
    private readonly string $emailAddress;
    private readonly string $firstName;
    private readonly string $lastName;
    private readonly string $password;
    private readonly ?string $resetPasswordId;

    public function __get(string $property)
    {
        return match($property) {
            'Id' => $this->id,
            'EmailAddress' => $this->emailAddress,
        };
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getResetPasswordId(): ?string
    {
        return $this->resetPasswordId;
    }
}