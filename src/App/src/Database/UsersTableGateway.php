<?php

namespace App\Database;

use App\Entity\User;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;

class UsersTableGateway extends AbstractTableGateway
{
    public function __construct(Adapter $adapter)
    {
        $this->table      = 'users';
        $this->featureSet = new FeatureSet();
        $this->adapter = $adapter;

        $resultSetPrototype = new HydratingResultSet();
        $hydrator = new ClassMethodsHydrator();
        $hydrator->setNamingStrategy(
            MapNamingStrategy::createFromAsymmetricMap(
                [
                    'firstName' => 'first_name',
                    'lastName' => 'last_name',
                    'emailAddress' => 'email_address',
                    'linkedinURN' => 'linkedin_urn',
                ],
                [
                    'first_name' => 'firstName',
                    'last_name' => 'lastName',
                    'email_address' => 'emailAddress',
                    'linkedin_urn' => 'linkedinURN',
                ],
            )
        );
        $resultSetPrototype->setHydrator($hydrator);
        $resultSetPrototype->setObjectPrototype(new User());
        $this->resultSetPrototype = $resultSetPrototype;

        $this->initialize();
    }

    public function createUser(array $userData): bool
    {
        return (bool)$this->insert(
            [
                'email_address' => $userData['email_address'],
                'password' => $this->getPasswordHash($userData['password']),
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
            ]
        );
    }

    public function addForgotPasswordFlag(string $emailAddress, string $resetPasswordId): bool
    {
        return (bool)$this->update(
            [
                'reset_password_id' => $resetPasswordId,
            ],
            [
                'email_address' => $emailAddress,
            ]
        );
    }

    public function resetPassword(string $emailAddress, string $password): bool
    {
        return (bool)$this->update(
            [
                'reset_password_id' => null,
                'password' => $this->getPasswordHash($password),
            ],
            [
                'email_address' => $emailAddress,
            ]
        );
    }

    public function findById(string $id): ?User
    {
        $users = $this->select([
            'id' => $id
        ]);

        if ($users->count()) {
            return $users->current();
        }

        return null;
    }

    public function findByEmail(string $emailAddress): ?User
    {
        $users = $this->select([
            'email_address' => $emailAddress
        ]);

        if ($users->count()) {
            return $users->current();
        }

        return null;
    }

    public function findByResetPasswordId(string $resetPasswordId): ?User
    {
        $users = $this->select([
            'reset_password_id' => $resetPasswordId,
        ]);

        if ($users->count()) {
            return $users->current();
        }

        return null;
    }

    public function findByEmailAndPassword(string $emailAddress): ?User
    {
        $users = $this->select([
            'email_address' => $emailAddress,
        ]);

        if ($users->count()) {
            return $users->current();
        }

        return null;
    }

    public function getPasswordHash(string $password): string
    {
        return password_hash(
            $password,
            PASSWORD_DEFAULT,
            $options = [
                'cost' => 14,
            ]
        );
    }
}