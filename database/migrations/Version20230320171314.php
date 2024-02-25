<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320171314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Sensor Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(TableName::TABLE_SENSOR);

        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
        ]);

        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('station_id', 'bigint');
        $table->addColumn('sensor_type_id', 'bigint');
        $table->addColumn('sensor_vendor_id', 'bigint', ['notnull' => false]);
        $table->addColumn('location', 'integer', ['notnull' => false]);
        $table->addColumn('unit', 'string', ['notnull' => false]);
        $table->addColumn('sr1', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('sr2', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('ar1', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('ar2', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('sensitivity', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('second_sensitivity', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('sensor_base', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('aux_base', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('vp_code', 'string', ['notnull' => false, 'length' => 50]);
        $table->addColumn('status', 'smallint');
        $table->addColumn('installed_at', 'datetime', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);

        // Foreign Keys
        $table->addForeignKeyConstraint(TableName::TABLE_STATION, ['station_id'], ['id']);
        $table->addForeignKeyConstraint(TableName::TABLE_SENSOR_TYPE, ['sensor_type_id'], ['id']);
        $table->addForeignKeyConstraint(TableName::TABLE_VENDOR, ['sensor_vendor_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_SENSOR);
    }
}
