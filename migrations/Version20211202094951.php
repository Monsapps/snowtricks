<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202094951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id_user INT AUTO_INCREMENT NOT NULL, name_user VARCHAR(255) NOT NULL, password_user VARCHAR(255) NOT NULL, email_user VARCHAR(255) NOT NULL, roles JSON NOT NULL, registration_date_user DATETIME NOT NULL, registration_token_user VARCHAR(255) NOT NULL, password_token_user VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9CA250C3E (name_user), UNIQUE INDEX UNIQ_1483A5E912A5F6CC (email_user), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}
