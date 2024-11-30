<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130161302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEDAB5A938 FOREIGN KEY (proposed_by_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A188FE64 ON user (nickname)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649A188FE64 ON user');
        $this->addSql('ALTER TABLE user DROP roles');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEDAB5A938');
    }
}
