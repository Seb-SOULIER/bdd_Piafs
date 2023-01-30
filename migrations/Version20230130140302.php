<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230130140302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, adherant_id INT DEFAULT NULL, intervenant_id INT DEFAULT NULL, add_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', comment LONGTEXT DEFAULT NULL, INDEX IDX_9474526CBE612E45 (adherant_id), INDEX IDX_9474526CAB9A1716 (intervenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CBE612E45 FOREIGN KEY (adherant_id) REFERENCES children (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CBE612E45');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAB9A1716');
        $this->addSql('DROP TABLE comment');
    }
}
