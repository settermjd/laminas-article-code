<?php

namespace App\Database;

use App\Entity\LinkedInPost;
use App\Entity\User;
use App\Hydrator\PostUserHydrator;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Platform\Sqlite;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Laminas\Hydrator\Strategy\ScalarTypeStrategy;

class ScheduledPostsTableGateway extends AbstractTableGateway
{
    public function __construct(Adapter $adapter)
    {
        $this->table      = 'scheduled_posts';
        $this->featureSet = new FeatureSet();
        $this->adapter = $adapter;

        $postHydrator = new ClassMethodsHydrator();
        $postHydrator->setNamingStrategy(
            MapNamingStrategy::createFromAsymmetricMap(
                [
                    'id' => 'id',
                    'title' => 'title',
                    'body' => 'body',
                    'publishOn' => 'publish_on',
                ],
                [
                    'id' => 'id',
                    'title' => 'title',
                    'body' => 'body',
                    'publish_on' => 'publishOn',
                ],
            )
        );
        $postHydrator->addStrategy(
            'id',
            ScalarTypeStrategy::createToInt()
        );
        $userHydrator = new PostUserHydrator();
        $hydrator = new AggregateHydrator();
        $hydrator->add($postHydrator);
        $hydrator->add($userHydrator);
        $this->resultSetPrototype = new HydratingResultSet($hydrator, new LinkedInPost());
        $this->initialize();
    }

    public function getAllPosts(): ResultSetInterface
    {
        $wherePredicate = (new Where())
            ->equalTo(
                new Expression("STRFTIME('%d/%m/%Y', publish_on)"),
                (new \DateTimeImmutable())->format('d/m/Y')
            );

        $sql = $this->getSql();
        $select = $sql->select();
        $select
            ->join(
            ['u' => 'users'],
            'u.id = user_id',
            ['first_name', 'last_name', 'linkedin_urn', 'email_address']
        );

        $select->where($wherePredicate);
        return $this->selectWith($select);
    }
}