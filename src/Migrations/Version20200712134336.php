<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200712134336 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, hotel_id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, tele VARCHAR(255) NOT NULL, cin_or_passport VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76F85E0677 (username), UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), UNIQUE INDEX UNIQ_880E0D76501A471A (cin_or_passport), INDEX IDX_880E0D763243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, hotel_id INT NOT NULL, duplicates INT NOT NULL, etage INT NOT NULL, superficie DOUBLE PRECISION NOT NULL, categorie VARCHAR(255) NOT NULL, capacity INT NOT NULL, INDEX IDX_C509E4FF3243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chambre_service (chambre_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_428BC92E9B177F54 (chambre_id), INDEX IDX_428BC92EED5CA9E6 (service_id), PRIMARY KEY(chambre_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, cin_or_passport VARCHAR(255) NOT NULL, tele VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C7440455501A471A (cin_or_passport), UNIQUE INDEX UNIQ_C7440455E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disponibility (id INT AUTO_INCREMENT NOT NULL, chambre_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_38BB92609B177F54 (chambre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, siteweb VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, distance_centre DOUBLE PRECISION NOT NULL, adresse VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, nbr_etoiles INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, chambre_id INT NOT NULL, reference VARCHAR(255) NOT NULL, date_reservation DATE NOT NULL, nbr_doublons INT NOT NULL, check_in DATE NOT NULL, check_out DATE NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_42C8495519EB6921 (client_id), INDEX IDX_42C849559B177F54 (chambre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saison (id INT AUTO_INCREMENT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, taux INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E19D9AD2A4D60759 (libelle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D763243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE chambre_service ADD CONSTRAINT FK_428BC92E9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chambre_service ADD CONSTRAINT FK_428BC92EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disponibility ADD CONSTRAINT FK_38BB92609B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chambre_service DROP FOREIGN KEY FK_428BC92E9B177F54');
        $this->addSql('ALTER TABLE disponibility DROP FOREIGN KEY FK_38BB92609B177F54');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849559B177F54');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D763243BB18');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF3243BB18');
        $this->addSql('ALTER TABLE chambre_service DROP FOREIGN KEY FK_428BC92EED5CA9E6');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE chambre_service');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE disponibility');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE saison');
        $this->addSql('DROP TABLE service');
    }
}
