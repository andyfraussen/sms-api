<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611195215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_class_group (subject_id INT NOT NULL, class_group_id INT NOT NULL, PRIMARY KEY(subject_id, class_group_id))');
        $this->addSql('CREATE INDEX IDX_131EBDD723EDC87 ON subject_class_group (subject_id)');
        $this->addSql('CREATE INDEX IDX_131EBDD74A9A1217 ON subject_class_group (class_group_id)');
        $this->addSql('ALTER TABLE subject_class_group ADD CONSTRAINT FK_131EBDD723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_class_group ADD CONSTRAINT FK_131EBDD74A9A1217 FOREIGN KEY (class_group_id) REFERENCES class_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_class_group DROP CONSTRAINT FK_131EBDD723EDC87');
        $this->addSql('ALTER TABLE subject_class_group DROP CONSTRAINT FK_131EBDD74A9A1217');
        $this->addSql('DROP TABLE subject_class_group');
    }
}
