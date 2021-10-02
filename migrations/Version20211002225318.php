<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002225318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sociedad_anonima (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, domicilio_legal VARCHAR(255) NOT NULL, domicilio_real VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, pais VARCHAR(255) DEFAULT NULL, estado VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sociedad_anonima_socio (id INT AUTO_INCREMENT NOT NULL, sociedad_anonima_id INT NOT NULL, socio_id INT NOT NULL, porcentaje_aporte INT NOT NULL, es_representante_legal TINYINT(1) DEFAULT NULL, INDEX IDX_C6A6115DFDB5AF2 (sociedad_anonima_id), INDEX IDX_C6A6115DDA04E6A9 (socio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE socio (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sociedad_anonima_socio ADD CONSTRAINT FK_C6A6115DFDB5AF2 FOREIGN KEY (sociedad_anonima_id) REFERENCES sociedad_anonima (id) ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sociedad_anonima_socio ADD CONSTRAINT FK_C6A6115DDA04E6A9 FOREIGN KEY (socio_id) REFERENCES socio (id) ON DELETE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sociedad_anonima_socio DROP FOREIGN KEY FK_C6A6115DFDB5AF2');
        $this->addSql('ALTER TABLE sociedad_anonima_socio DROP FOREIGN KEY FK_C6A6115DDA04E6A9');
        $this->addSql('DROP TABLE sociedad_anonima');
        $this->addSql('DROP TABLE sociedad_anonima_socio');
        $this->addSql('DROP TABLE socio');
    }
}
