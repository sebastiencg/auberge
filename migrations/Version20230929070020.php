<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929070020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE mot_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reservation_id_seq CASCADE');
        $this->addSql('CREATE TABLE bed_equipment (bed_id INT NOT NULL, equipment_id INT NOT NULL, PRIMARY KEY(bed_id, equipment_id))');
        $this->addSql('CREATE INDEX IDX_4C09CBDD88688BB9 ON bed_equipment (bed_id)');
        $this->addSql('CREATE INDEX IDX_4C09CBDD517FE9FE ON bed_equipment (equipment_id)');
        $this->addSql('CREATE TABLE equipment (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE bed_equipment ADD CONSTRAINT FK_4C09CBDD88688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bed_equipment ADD CONSTRAINT FK_4C09CBDD517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE mot');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE mot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mot (id INT NOT NULL, mot VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE bed_equipment DROP CONSTRAINT FK_4C09CBDD88688BB9');
        $this->addSql('ALTER TABLE bed_equipment DROP CONSTRAINT FK_4C09CBDD517FE9FE');
        $this->addSql('DROP TABLE bed_equipment');
        $this->addSql('DROP TABLE equipment');
    }
}
