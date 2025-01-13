<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216105753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checkpoint (id INT AUTO_INCREMENT NOT NULL, roadtrip_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, google_maps_coordinates LONGTEXT NOT NULL, arrival_date DATETIME NOT NULL, departure_date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F00F7BEE646B2FB (roadtrip_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, roadtrip_id_id INT DEFAULT NULL, url LONGTEXT NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C53D045FE646B2FB (roadtrip_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roadtrip (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, user_id_id INT NOT NULL, vehicle_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_EA152DD3DA5256D (image_id), INDEX IDX_EA152DD9D86650F (user_id_id), INDEX IDX_EA152DD545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicles (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE checkpoint ADD CONSTRAINT FK_F00F7BEE646B2FB FOREIGN KEY (roadtrip_id_id) REFERENCES roadtrip (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FE646B2FB FOREIGN KEY (roadtrip_id_id) REFERENCES roadtrip (id)');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DD3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DD9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DD545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkpoint DROP FOREIGN KEY FK_F00F7BEE646B2FB');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FE646B2FB');
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DD3DA5256D');
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DD9D86650F');
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DD545317D1');
        $this->addSql('DROP TABLE checkpoint');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE roadtrip');
        $this->addSql('DROP TABLE vehicles');
    }
}
