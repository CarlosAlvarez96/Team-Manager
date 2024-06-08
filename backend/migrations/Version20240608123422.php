<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608123422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE squad ADD manager_id INT DEFAULT NULL, ADD money NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE squad ADD CONSTRAINT FK_CFD0FFE7783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CFD0FFE7783E3463 ON squad (manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE squad DROP FOREIGN KEY FK_CFD0FFE7783E3463');
        $this->addSql('DROP INDEX IDX_CFD0FFE7783E3463 ON squad');
        $this->addSql('ALTER TABLE squad DROP manager_id, DROP money');
    }
}
