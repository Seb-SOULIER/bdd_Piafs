<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130090617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD lastname VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD birthdate DATE DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD address VARCHAR(255) NOT NULL, ADD zipcode INT DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL, ADD subcribe_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_active TINYINT(1) NOT NULL, ADD restore_code VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP lastname, DROP firstname, DROP birthdate, DROP avatar, DROP address, DROP zipcode, DROP city, DROP phone, DROP subcribe_at, DROP is_active, DROP restore_code');
    }
}
