<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021182259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_project (subject_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_8A317C4C23EDC87 (subject_id), INDEX IDX_8A317C4C166D1F9C (project_id), PRIMARY KEY(subject_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subject_project ADD CONSTRAINT FK_8A317C4C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject_project ADD CONSTRAINT FK_8A317C4C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project CHANGE student_id student_id INT DEFAULT NULL, CHANGE proposed_by_id proposed_by_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_project DROP FOREIGN KEY FK_8A317C4C23EDC87');
        $this->addSql('ALTER TABLE subject_project DROP FOREIGN KEY FK_8A317C4C166D1F9C');
        $this->addSql('DROP TABLE subject_project');
        $this->addSql('ALTER TABLE project CHANGE student_id student_id INT NOT NULL, CHANGE proposed_by_id proposed_by_id INT NOT NULL');
    }
}
