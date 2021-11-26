<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126011323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, INDEX IDX_D8F0A91E12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_image (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, url VARCHAR(255) NOT NULL, upload_date DATETIME NOT NULL, INDEX IDX_E1204E0B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_message (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, trick_id INT NOT NULL, content LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, INDEX IDX_D5E04927F675F31B (author_id), INDEX IDX_D5E04927B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_video (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, embed_code LONGTEXT NOT NULL, INDEX IDX_B7E8DA93B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, registration_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E12469DE2 FOREIGN KEY (category_id) REFERENCES trick_category (id)');
        $this->addSql('ALTER TABLE trick_image ADD CONSTRAINT FK_E1204E0B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick_message ADD CONSTRAINT FK_D5E04927F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE trick_message ADD CONSTRAINT FK_D5E04927B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick_video ADD CONSTRAINT FK_B7E8DA93B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_image DROP FOREIGN KEY FK_E1204E0B281BE2E');
        $this->addSql('ALTER TABLE trick_message DROP FOREIGN KEY FK_D5E04927B281BE2E');
        $this->addSql('ALTER TABLE trick_video DROP FOREIGN KEY FK_B7E8DA93B281BE2E');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E12469DE2');
        $this->addSql('ALTER TABLE trick_message DROP FOREIGN KEY FK_D5E04927F675F31B');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE trick_category');
        $this->addSql('DROP TABLE trick_image');
        $this->addSql('DROP TABLE trick_message');
        $this->addSql('DROP TABLE trick_video');
        $this->addSql('DROP TABLE `user`');
    }
}
