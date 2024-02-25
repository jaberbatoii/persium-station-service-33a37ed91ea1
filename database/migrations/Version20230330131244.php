<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330131244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Config TimescaleDB';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("SELECT create_hypertable('".TableName::TABLE_SENSOR_DATA."', 'timestamp', migrate_data => true)");
        $this->addSql("SELECT create_hypertable('".TableName::TABLE_AQI_DATA."', 'timestamp', migrate_data => true)");
        $this->addSql("SELECT create_hypertable('".TableName::TABLE_SENSOR_AQI_DATA."', 'timestamp', migrate_data => true)");
    }

    public function down(Schema $schema): void
    {
    }
}
