<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220220161934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE creation_date creation_date DATETIME NOT NULL, CHANGE update_date update_date DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE is_draft is_draft TINYINT(1) NOT NULL, CHANGE slug slug VARCHAR(255) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE creation_date creation_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL, CHANGE is_draft is_draft TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
