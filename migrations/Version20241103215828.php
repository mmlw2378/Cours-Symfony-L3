<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103215828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F2C56620');
        $this->addSql('DROP INDEX UNIQ_C7440455F2C56620 ON client');
        $this->addSql('ALTER TABLE client DROP compte_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455E7769B0F ON client (surname)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455450FF010 ON client (telephone)');
        $this->addSql('ALTER TABLE user ADD client_id INT DEFAULT NULL, ADD roles JSON NOT NULL, ADD is_blocked TINYINT(1) NOT NULL, ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE login login VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON user (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C7440455E7769B0F ON client');
        $this->addSql('DROP INDEX UNIQ_C7440455450FF010 ON client');
        $this->addSql('ALTER TABLE client ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F2C56620 FOREIGN KEY (compte_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455F2C56620 ON client (compte_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('DROP INDEX UNIQ_8D93D64919EB6921 ON user');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_LOGIN ON user');
        $this->addSql('ALTER TABLE user DROP client_id, DROP roles, DROP is_blocked, DROP create_at, DROP update_at, CHANGE login login VARCHAR(25) NOT NULL, CHANGE password password VARCHAR(15) NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE prenom prenom VARCHAR(100) NOT NULL');
    }
}
