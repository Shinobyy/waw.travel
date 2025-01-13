<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216112905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DD3DA5256D');
        $this->addSql('DROP INDEX IDX_EA152DD3DA5256D ON roadtrip');
        $this->addSql('ALTER TABLE roadtrip CHANGE image_id cover_image_id INT NOT NULL');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DDE5A0E336 FOREIGN KEY (cover_image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_EA152DDE5A0E336 ON roadtrip (cover_image_id)');
        $this->addSql('ALTER TABLE vehicles CHANGE type type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roadtrip DROP FOREIGN KEY FK_EA152DDE5A0E336');
        $this->addSql('DROP INDEX IDX_EA152DDE5A0E336 ON roadtrip');
        $this->addSql('ALTER TABLE roadtrip CHANGE cover_image_id image_id INT NOT NULL');
        $this->addSql('ALTER TABLE roadtrip ADD CONSTRAINT FK_EA152DD3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_EA152DD3DA5256D ON roadtrip (image_id)');
        $this->addSql('ALTER TABLE vehicles CHANGE type type INT NOT NULL');
    }
}
