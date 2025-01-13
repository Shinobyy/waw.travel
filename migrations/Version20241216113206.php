<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216113206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkpoint ADD roadtrip_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE checkpoint ADD CONSTRAINT FK_F00F7BECA4CCFF5 FOREIGN KEY (roadtrip_id) REFERENCES roadtrip (id)');
        $this->addSql('CREATE INDEX IDX_F00F7BECA4CCFF5 ON checkpoint (roadtrip_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkpoint DROP FOREIGN KEY FK_F00F7BECA4CCFF5');
        $this->addSql('DROP INDEX IDX_F00F7BECA4CCFF5 ON checkpoint');
        $this->addSql('ALTER TABLE checkpoint DROP roadtrip_id');
    }
}
