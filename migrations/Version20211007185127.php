<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211007185127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP FOREIGN KEY FK_BC531D11BC62652');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP FOREIGN KEY FK_BC531D11FDB5AF2');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11BC62652 FOREIGN KEY (pais_estado_id) REFERENCES pais_estado (id)');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11FDB5AF2 FOREIGN KEY (sociedad_anonima_id) REFERENCES sociedad_anonima (id)');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD PRIMARY KEY (sociedad_anonima_id, pais_estado_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP FOREIGN KEY FK_BC531D11FDB5AF2');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP FOREIGN KEY FK_BC531D11BC62652');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11FDB5AF2 FOREIGN KEY (sociedad_anonima_id) REFERENCES sociedad_anonima (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11BC62652 FOREIGN KEY (pais_estado_id) REFERENCES pais_estado (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD PRIMARY KEY (pais_estado_id, sociedad_anonima_id)');
    }
}
