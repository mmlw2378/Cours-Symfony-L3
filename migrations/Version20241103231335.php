<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103231335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F2C56620 FOREIGN KEY (compte_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455F2C56620 ON client (compte_id)');
        $this->addSql('ALTER TABLE user DROP is_blocked, CHANGE nom nom VARCHAR(35) NOT NULL, CHANGE prenom prenom VARCHAR(24) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F2C56620');
        $this->addSql('DROP INDEX UNIQ_C7440455F2C56620 ON client');
        $this->addSql('ALTER TABLE client DROP compte_id');
        $this->addSql('ALTER TABLE user ADD is_blocked TINYINT(1) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL');
    }
}
