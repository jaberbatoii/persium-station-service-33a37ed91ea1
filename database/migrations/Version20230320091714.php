<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320091714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Station Table';
    }

    public function up(Schema $schema): void
    {

        $table = $schema->createTable(TableName::TABLE_STATION);

        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
        ]);

        $table->addColumn('uuid', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('address', 'text', ['notnull' => false]);
        $table->addColumn('source', 'string', ['length' => 30]);
        $table->addColumn('source_id', 'string', ['length' => 255]);
        $table->addColumn('photo_url', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('status', 'smallint');
        $table->addColumn('latitude', 'float', ['precision' => 11, 'scale' => 8, 'notnull' => true]);
        $table->addColumn('longitude', 'float', ['precision' => 11, 'scale' => 8, 'notnull' => true]);
        $table->addColumn('altitude', 'float', ['precision' => 11, 'scale' => 8, 'notnull' => true]);
        $table->addColumn('installed_at', 'datetime', ['notnull' => true]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid']);
        $table->addUniqueIndex(['source', 'source_id']);
        $table->addIndex(['name']);
        $table->addIndex(['status']);
        $table->addIndex(['latitude', 'longitude']);

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(TableName::TABLE_STATION);
    }
}
