<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006064623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE mot_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN image.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE mot');
        $this->addSql('ALTER TABLE room ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B3DA5256D ON room (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B3DA5256D');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE mot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mot (id INT NOT NULL, mot VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP INDEX UNIQ_729F519B3DA5256D');
        $this->addSql('ALTER TABLE room DROP image_id');
    }
}
