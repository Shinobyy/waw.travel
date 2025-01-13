<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216133624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DDE5A0E336');
        $this->addSql('DROP INDEX IDX_EA152DDE5A0E336 ON roadtrip');
        $this->addSql('ALTER TABLE roadtrip ADD cover_image LONGTEXT NOT NULL, DROP cover_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roadtrip ADD cover_image_id INT NOT NULL, DROP cover_image');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DDE5A0E336 FOREIGN KEY (cover_image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_EA152DDE5A0E336 ON roadtrip (cover_image_id)');
    }
}
