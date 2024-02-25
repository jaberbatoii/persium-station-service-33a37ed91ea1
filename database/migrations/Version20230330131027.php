<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330131027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Station Sensor AQI Data Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(TableName::TABLE_SENSOR_AQI_DATA);
        $table->addColumn('station_sensor_id', 'bigint');
        $table->addColumn('timestamp', 'datetime');
        $table->addColumn('type', 'smallint');
        $table->addColumn('value','integer');

        $table->setPrimaryKey(['station_sensor_id', 'type', 'timestamp']);
        $table->addForeignKeyConstraint(TableName::TABLE_SENSOR, ['station_sensor_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_SENSOR_AQI_DATA);
    }
}
