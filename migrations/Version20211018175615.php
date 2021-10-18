<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018175615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sociedad_anonima ADD motivo_rechazo VARCHAR(255) NOT NULL, ADD plazo_correccion INT NOT NULL, ADD numero_expediente INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E46DEDF6B56139B0 ON sociedad_anonima (numero_expediente)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E46DEDF6B56139B0 ON sociedad_anonima');
        $this->addSql('ALTER TABLE sociedad_anonima DROP motivo_rechazo, DROP plazo_correccion, DROP numero_expediente');
    }
}
