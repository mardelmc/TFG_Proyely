<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204172917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_teacher (group_id INT NOT NULL, teacher_id INT NOT NULL, INDEX IDX_36F6F2D9FE54D947 (group_id), INDEX IDX_36F6F2D941807E1D (teacher_id), PRIMARY KEY(group_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_teacher ADD CONSTRAINT FK_36F6F2D9FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_teacher ADD CONSTRAINT FK_36F6F2D941807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutors DROP FOREIGN KEY FK_639001EF41807E1D');
        $this->addSql('ALTER TABLE tutors DROP FOREIGN KEY FK_639001EFFE54D947');
        $this->addSql('DROP TABLE tutors');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutors (teacher_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_639001EFFE54D947 (group_id), INDEX IDX_639001EF41807E1D (teacher_id), PRIMARY KEY(teacher_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tutors ADD CONSTRAINT FK_639001EF41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutors ADD CONSTRAINT FK_639001EFFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_teacher DROP FOREIGN KEY FK_36F6F2D9FE54D947');
        $this->addSql('ALTER TABLE group_teacher DROP FOREIGN KEY FK_36F6F2D941807E1D');
        $this->addSql('DROP TABLE group_teacher');
    }
}
