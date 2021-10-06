<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005191355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pais_estado_sociedad_anonima (pais_estado_id INT NOT NULL, sociedad_anonima_id INT NOT NULL, INDEX IDX_BC531D11BC62652 (pais_estado_id), INDEX IDX_BC531D11FDB5AF2 (sociedad_anonima_id), PRIMARY KEY(pais_estado_id, sociedad_anonima_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11BC62652 FOREIGN KEY (pais_estado_id) REFERENCES pais_estado (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pais_estado_sociedad_anonima ADD CONSTRAINT FK_BC531D11FDB5AF2 FOREIGN KEY (sociedad_anonima_id) REFERENCES sociedad_anonima (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pais_estado_sociedad_anonima');
    }
}
