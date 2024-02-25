<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426094854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table wind map';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(TableName::TABLE_WIND_POINTS);
        $table->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table->addColumn('latitude', 'float', ['precision' => 11, 'scale' => 8, 'notnull' => true]);
        $table->addColumn('longitude', 'float', ['precision' => 11, 'scale' => 8, 'notnull' => true]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);

        $table2 = $schema->createTable(TableName::TABLE_WIND_DATA);
        $table2->addColumn('id', 'bigint', ['autoincrement' => true]);
        $table2->addColumn('wind_point_id', 'bigint');
        $table2->addColumn('speed', 'float', ['precision' => 11, 'scale' => 7, 'notnull' => true]);
        $table2->addColumn('direction', 'float', ['precision' => 11, 'scale' => 7, 'notnull' => true]);
        $table2->addColumn('timestamp', 'datetime');
        $table2->setPrimaryKey(['id']);
        $table2->addForeignKeyConstraint(TableName::TABLE_WIND_POINTS, ['wind_point_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_WIND_DATA);
        $schema->dropTable(TableName::TABLE_WIND_POINTS);
    }
}
