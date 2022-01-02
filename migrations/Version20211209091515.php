<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209091515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avatars (id_avatar INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, path_avatar VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B0C985206B3CA4B (id_user), PRIMARY KEY(id_avatar)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id_comment INT AUTO_INCREMENT NOT NULL, id_trick INT DEFAULT NULL, id_user INT DEFAULT NULL, content_comment VARCHAR(255) NOT NULL, date_comment DATETIME NOT NULL, INDEX IDX_5F9E962A3675E82E (id_trick), INDEX IDX_5F9E962A6B3CA4B (id_user), PRIMARY KEY(id_comment)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks (id_trick INT AUTO_INCREMENT NOT NULL, id_trick_type INT DEFAULT NULL, name_trick VARCHAR(255) NOT NULL, description_trick VARCHAR(255) NOT NULL, creation_date_trick DATETIME NOT NULL, modification_date_trick DATETIME NOT NULL, UNIQUE INDEX UNIQ_E1D902C116D6FB5B (name_trick), INDEX IDX_E1D902C167E7E44E (id_trick_type), PRIMARY KEY(id_trick)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_images (id_trick_image INT AUTO_INCREMENT NOT NULL, id_trick INT DEFAULT NULL, path_trick_image VARCHAR(255) NOT NULL, is_main_trick_image TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_D4A857A83675E82E (id_trick), PRIMARY KEY(id_trick_image)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_medias (id_trick_media INT AUTO_INCREMENT NOT NULL, id_trick INT DEFAULT NULL, path_trick_media VARCHAR(255) NOT NULL, INDEX IDX_266546433675E82E (id_trick), PRIMARY KEY(id_trick_media)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_type (id_trick_type INT AUTO_INCREMENT NOT NULL, name_trick_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6ABAE2C05AC3B41B (name_trick_type), PRIMARY KEY(id_trick_type)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatars ADD CONSTRAINT FK_B0C985206B3CA4B FOREIGN KEY (id_user) REFERENCES users (id_user)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A3675E82E FOREIGN KEY (id_trick) REFERENCES tricks (id_trick)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A6B3CA4B FOREIGN KEY (id_user) REFERENCES users (id_user)');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C167E7E44E FOREIGN KEY (id_trick_type) REFERENCES tricks_type (id_trick_type)');
        $this->addSql('ALTER TABLE tricks_images ADD CONSTRAINT FK_D4A857A83675E82E FOREIGN KEY (id_trick) REFERENCES tricks (id_trick)');
        $this->addSql('ALTER TABLE tricks_medias ADD CONSTRAINT FK_266546433675E82E FOREIGN KEY (id_trick) REFERENCES tricks (id_trick)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A3675E82E');
        $this->addSql('ALTER TABLE tricks_images DROP FOREIGN KEY FK_D4A857A83675E82E');
        $this->addSql('ALTER TABLE tricks_medias DROP FOREIGN KEY FK_266546433675E82E');
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C167E7E44E');
        $this->addSql('DROP TABLE avatars');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE tricks');
        $this->addSql('DROP TABLE tricks_images');
        $this->addSql('DROP TABLE tricks_medias');
        $this->addSql('DROP TABLE tricks_type');
    }
}
