<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425155007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Failed Jobs Table';
    }

    public function up(Schema $schema): void
    {
        $schema->createTable(TableName::TABLE_FAILED_JOB);
        $table = $schema->getTable(TableName::TABLE_FAILED_JOB);
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('connection', 'string', ['length' => 255]);
        $table->addColumn('queue', 'string', ['length' => 255]);
        $table->addColumn('payload', 'text');
        $table->addColumn('exception', 'text');
        $table->addColumn('failed_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_FAILED_JOB);
    }
}
