<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200716174458 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prix_saison (id INT AUTO_INCREMENT NOT NULL, chambre_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, prix DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_4CE3E61B9B177F54 (chambre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prix_saison ADD CONSTRAINT FK_4CE3E61B9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('DROP TABLE saison');
        $this->addSql('ALTER TABLE client CHANGE tele tele VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE hotel CHANGE recommended recommended TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE saison (id INT AUTO_INCREMENT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, taux INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE prix_saison');
        $this->addSql('ALTER TABLE client CHANGE tele tele VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE hotel CHANGE recommended recommended TINYINT(1) DEFAULT \'NULL\'');
    }
}
