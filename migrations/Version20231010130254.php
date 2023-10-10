<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010130254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE mot_id_seq CASCADE');
        $this->addSql('ALTER TABLE reservation ADD mail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation DROP email');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C8776F01 FOREIGN KEY (mail_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C84955C8776F01 ON reservation (mail_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE mot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955C8776F01');
        $this->addSql('DROP INDEX IDX_42C84955C8776F01');
        $this->addSql('ALTER TABLE reservation ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP mail_id');
    }
}
