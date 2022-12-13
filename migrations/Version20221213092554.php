<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213092554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, intervenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date DATE DEFAULT NULL, hour_start DATETIME DEFAULT NULL, hour_stop DATETIME DEFAULT NULL, place INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_E1BB1823AB9A1716 (intervenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE atelier_user (atelier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4D145FAA82E2CF35 (atelier_id), INDEX IDX_4D145FAAA76ED395 (user_id), PRIMARY KEY(atelier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier ADD CONSTRAINT FK_E1BB1823AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAA82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP FOREIGN KEY FK_E1BB1823AB9A1716');
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAA82E2CF35');
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAAA76ED395');
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE atelier_user');
    }
}
