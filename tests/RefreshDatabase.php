<?php
declare(strict_types = 1);

namespace Tests;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Migrator;
use Doctrine\ORM\EntityManager;


trait RefreshDatabase
{
    public function refreshDatabase(): void
    {
        $this->beforeRefreshingDatabase();
    }

    private function beforeRefreshingDatabase(): void
    {
        $em = app()->make(EntityManager::class);
        $schema_tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schema_tool->updateSchema($metadata);
    }

    private function afterRefreshingDatabase(): void
    {
        $em = app()->make(EntityManager::class);
        $schema_tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schema_tool->dropDatabase();
    }
}
