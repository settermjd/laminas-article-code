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

    public function __construct(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    public function __get(string $property)
    {
        return match($property) {
            'id', 'emailAddress', 'firstName', 'lastName', 'password', 'resetPasswordId' => $this->$property,
            'fullName' => sprintf(
                "%s %s",
                $this->firstName,
                $this->lastName
            )
        };
    }
}