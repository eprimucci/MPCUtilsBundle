<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140502145909 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE observation (id INT AUTO_INCREMENT NOT NULL, observatory_id INT DEFAULT NULL, mpnumber VARCHAR(5) DEFAULT NULL, temp_designation VARCHAR(7) DEFAULT NULL, discovery TINYINT(1) NOT NULL, utdate VARCHAR(18) NOT NULL, ra NUMERIC(9, 6) DEFAULT NULL, decli NUMERIC(9, 6) DEFAULT NULL, mag NUMERIC(6, 3) DEFAULT NULL, vband VARCHAR(3) DEFAULT NULL, obs_type VARCHAR(15) NOT NULL, obs_datetime DATETIME NOT NULL, created DATETIME NOT NULL, INDEX IDX_C576DBE097EE0280 (observatory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE observation ADD CONSTRAINT FK_C576DBE097EE0280 FOREIGN KEY (observatory_id) REFERENCES observatory (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE observation");
    }
}
