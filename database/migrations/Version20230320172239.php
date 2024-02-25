<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320172239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Station AQI Data Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(TableName::TABLE_AQI_DATA);
        $table->addColumn('station_id', 'bigint');
        $table->addColumn('timestamp', 'datetime');
        $table->addColumn('type', 'smallint');
        $table->addColumn('value','integer');
        $table->addColumn('factor','string', ['length' => 30]);

        $table->setPrimaryKey(['station_id', 'type', 'timestamp']);
        $table->addForeignKeyConstraint(TableName::TABLE_STATION, ['station_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_AQI_DATA);
    }
}
