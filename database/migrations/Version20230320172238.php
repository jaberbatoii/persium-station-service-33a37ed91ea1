<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320172238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Station Sensor Data Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(TableName::TABLE_SENSOR_DATA);
        $table->addColumn('station_sensor_id', 'bigint');
        $table->addColumn('value', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('ugm3', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('ppb', 'float', ['notnull' => false, 'precision' => 30, 'scale' => 20]);
        $table->addColumn('timestamp', 'datetime');

        $table->setPrimaryKey(['station_sensor_id', 'timestamp']);
        // Foreign Keys
        $table->addForeignKeyConstraint(TableName::TABLE_SENSOR, ['station_sensor_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_SENSOR_DATA);
    }
}
