<?php

namespace App\Entity;

class User
{
    private int $id;
    private string $emailAddress;
    private string $firstName;
    private string $lastName;
    private string $password;
    private ?string $resetPasswordId;
    private ?string $linkedinURN;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = (int) $id;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getLinkedinURN(): string
    {
        return $this->linkedinURN;
    }

    public function setLinkedinURN(?string $linkedinURN = null): void
    {
        $this->linkedinURN = $linkedinURN;
    }

    public function isIntegratedWithLinkedIn(): bool
    {
        return (! empty($this->linkedinURN));
    }

    public function toArray(): array
    {
        return [];
    }

    public function getResetPasswordId(): ?string
    {
        return $this->resetPasswordId;
    }

    public function setResetPasswordId(?string $resetPasswordId): void
    {
        $this->resetPasswordId = $resetPasswordId;
    }
}