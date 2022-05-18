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
}