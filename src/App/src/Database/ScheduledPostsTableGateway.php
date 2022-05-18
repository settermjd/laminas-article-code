<?php

namespace App\Database;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;

class ScheduledPostsTableGateway extends AbstractTableGateway
{
    public function __construct()
    {
        $this->table      = 'scheduled_posts';
        $this->featureSet = new FeatureSet();
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        $this->initialize();
    }
}