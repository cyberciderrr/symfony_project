<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204052628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EEC31A529C');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEC31A529C FOREIGN KEY (project_group_id) REFERENCES project_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT fk_527edb25166d1f9c');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT fk_527edb25166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT fk_2fb3d0eec31a529c');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT fk_2fb3d0eec31a529c FOREIGN KEY (project_group_id) REFERENCES project_group (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
