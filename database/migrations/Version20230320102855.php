<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320102855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create Sensor Vendor Table";
    }

    public function up(Schema $schema): void
    {
        $schema->createTable(TableName::TABLE_VENDOR);
        $table = $schema->getTable(TableName::TABLE_VENDOR);
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('display_name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_VENDOR);
    }
}
