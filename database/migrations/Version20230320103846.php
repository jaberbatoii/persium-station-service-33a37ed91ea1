<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320103846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create Cross Sensitivity Table";
    }

    public function up(Schema $schema): void
    {
        $schema->createTable(TableName::TABLE_CROSS_SENSITIVITY);
        $table = $schema->getTable(TableName::TABLE_CROSS_SENSITIVITY);
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 50]);
        $table->addColumn('sensor_type_id', 'bigint');
        $table->addColumn('adjustment', 'float', ['precision' => 40, 'scale' => 30]);
        $table->addColumn('concentration', 'float', ['precision' => 40, 'scale' => 30]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);
        $table->addForeignKeyConstraint(TableName::TABLE_SENSOR_TYPE,
            ['sensor_type_id'],
            ['id']
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_CROSS_SENSITIVITY);
    }
}
