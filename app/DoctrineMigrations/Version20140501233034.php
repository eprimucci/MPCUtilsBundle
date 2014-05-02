<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140501233034 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE observation_stats (id INT AUTO_INCREMENT NOT NULL, observatory_id INT DEFAULT NULL, startDate DATETIME NOT NULL, endDate DATETIME NOT NULL, data VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_EC2E751797EE0280 (observatory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE observation_stats ADD CONSTRAINT FK_EC2E751797EE0280 FOREIGN KEY (observatory_id) REFERENCES observatory (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE observation_stats");
    }
}
