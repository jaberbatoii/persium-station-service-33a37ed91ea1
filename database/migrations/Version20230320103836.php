<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320103836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create Sensor Type Table";
    }

    public function up(Schema $schema): void
    {
        $schema->createTable(TableName::TABLE_SENSOR_TYPE);
        $table = $schema->getTable(TableName::TABLE_SENSOR_TYPE);
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('raw_name', 'string', ['length' => 50]);
        $table->addColumn('name', 'string', ['length' => 50]);
        $table->addColumn('display_name', 'string', ['length' => 100]);
        $table->addColumn('type', 'smallint');
        $table->addColumn('e_multi', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('e_pow_multi', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('m_multi', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('p_nom', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('molar_mass', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('constant', 'float', ['notnull' => false, 'precision' => 40, 'scale' => 30]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);
        $table->addIndex(['name']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_SENSOR_TYPE);
    }
}
