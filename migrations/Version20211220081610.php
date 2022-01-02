<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220081610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E1D902C116D6FB5B ON tricks');
        $this->addSql('DROP INDEX UNIQ_E1D902C1D082DE3 ON tricks');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D902C1D082DE3 ON tricks (slug_trick)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E1D902C1D082DE3 ON tricks');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D902C116D6FB5B ON tricks (name_trick)');
        $this->addSql('CREATE INDEX UNIQ_E1D902C1D082DE3 ON tricks (slug_trick)');
    }
}
