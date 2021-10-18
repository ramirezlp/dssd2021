<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018164157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sociedad_anonima ADD solicitante_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sociedad_anonima ADD CONSTRAINT FK_E46DEDF6C680A87 FOREIGN KEY (solicitante_id) REFERENCES user (id) ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E46DEDF6C680A87 ON sociedad_anonima (solicitante_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sociedad_anonima DROP FOREIGN KEY FK_E46DEDF6C680A87');
        $this->addSql('DROP INDEX IDX_E46DEDF6C680A87 ON sociedad_anonima');
        $this->addSql('ALTER TABLE sociedad_anonima DROP solicitante_id');
    }
}
