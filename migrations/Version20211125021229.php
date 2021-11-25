<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125021229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historico_estado_sociedad_anonima (id INT AUTO_INCREMENT NOT NULL, sociedad_anonima_id INT NOT NULL, estado VARCHAR(255) NOT NULL, INDEX IDX_B1FE7126FDB5AF2 (sociedad_anonima_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historico_estado_sociedad_anonima ADD CONSTRAINT FK_B1FE7126FDB5AF2 FOREIGN KEY (sociedad_anonima_id) REFERENCES sociedad_anonima (id) ON DELETE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE historico_estado_sociedad_anonima');
    }
}
