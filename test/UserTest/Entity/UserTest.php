<?php

namespace UserTest\Entity;

use User\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCanRetrieveAllProperties()
    {
        $properties = [
            'id' => 1,
            'emailAddress' => 'matthew@matthewsetter.com',
            'firstName' => 'Matthew',
            'lastName' => 'Setter',
            'password' => '12345',
            'resetPasswordId' => '54321',
        ];
        $user = new User($properties);

        foreach ($properties as $property => $value) {
            $this->assertSame($value, $user->$property);
        }

        $fullName = sprintf(
            "%s %s",
            $properties['firstName'],
            $properties['lastName']
        );
        $this->assertSame($fullName, $user->fullName);
    }
}
