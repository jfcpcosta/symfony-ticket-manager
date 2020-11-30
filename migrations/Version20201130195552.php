<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201130195552 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket CHANGE severity severity_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F7527401 FOREIGN KEY (severity_id) REFERENCES severity (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3F7527401 ON ticket (severity_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F7527401');
        $this->addSql('DROP INDEX IDX_97A0ADA3F7527401 ON ticket');
        $this->addSql('ALTER TABLE ticket CHANGE severity_id severity INT NOT NULL');
    }
}
