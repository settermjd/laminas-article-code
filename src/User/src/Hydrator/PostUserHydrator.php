<?php

namespace User\Hydrator;

use User\Entity\LinkedInPost;
use User\Entity\User;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Laminas\Hydrator\Strategy\ScalarTypeStrategy;

class PostUserHydrator extends ClassMethodsHydrator
{
    public function __construct()
    {
        $this->setNamingStrategy(
            MapNamingStrategy::createFromAsymmetricMap(
                [
                    'id' => 'user_id',
                    'firstName' => 'first_name',
                    'lastName' => 'last_name',
                    'emailAddress' => 'email_address',
                    'linkedinURN' => 'linkedin_urn',
                ],
                [
                    'user_id' => 'id',
                    'first_name' => 'firstName',
                    'last_name' => 'lastName',
                    'email_address' => 'emailAddress',
                    'linkedin_urn' => 'linkedinURN',
                ],
            )
        );
        $this->addStrategy(
            'id',
            ScalarTypeStrategy::createToInt()
        );
    }

    public function hydrate($data, $object)
    {
        if (! $object instanceof LinkedInPost) {
            return $object;
        }
        $user = parent::hydrate($data, new User());
        $object->setUser($user);

        return $object;
    }

    public function extract($object): array
    {
        if (! $object instanceof LinkedInPost) {
            return array();
        }

        return $object->getUser()->toArray();
    }
}