<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127193948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_message DROP FOREIGN KEY FK_D5E04927B281BE2E');
        $this->addSql('DROP INDEX IDX_D5E04927B281BE2E ON trick_message');
        $this->addSql('ALTER TABLE trick_message DROP trick_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_message ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick_message ADD CONSTRAINT FK_D5E04927B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_D5E04927B281BE2E ON trick_message (trick_id)');
    }
}
