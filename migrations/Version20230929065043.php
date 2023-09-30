<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929065043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bed_equipment (bed_id INT NOT NULL, equipment_id INT NOT NULL, PRIMARY KEY(bed_id, equipment_id))');
        $this->addSql('CREATE INDEX IDX_4C09CBDD88688BB9 ON bed_equipment (bed_id)');
        $this->addSql('CREATE INDEX IDX_4C09CBDD517FE9FE ON bed_equipment (equipment_id)');
        $this->addSql('CREATE TABLE equipment (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reservation (id INT NOT NULL, bed_id INT DEFAULT NULL, naÃme VARCHAR(255) NOT NULL, price INT NOT NULL, date_in TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_out TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, day INT NOT NULL, statue BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C8495588688BB9 ON reservation (bed_id)');
        $this->addSql('COMMENT ON COLUMN reservation.date_in IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservation.date_out IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE bed_equipment ADD CONSTRAINT FK_4C09CBDD88688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bed_equipment ADD CONSTRAINT FK_4C09CBDD517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495588688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room ADD price INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bed_equipment DROP CONSTRAINT FK_4C09CBDD88688BB9');
        $this->addSql('ALTER TABLE bed_equipment DROP CONSTRAINT FK_4C09CBDD517FE9FE');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495588688BB9');
        $this->addSql('DROP TABLE bed_equipment');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE room DROP price');
    }
}
