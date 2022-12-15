<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221215081513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier_children (atelier_id INT NOT NULL, children_id INT NOT NULL, INDEX IDX_641FB35F82E2CF35 (atelier_id), INDEX IDX_641FB35F3D3D2749 (children_id), PRIMARY KEY(atelier_id, children_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier_children ADD CONSTRAINT FK_641FB35F82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE atelier_children ADD CONSTRAINT FK_641FB35F3D3D2749 FOREIGN KEY (children_id) REFERENCES children (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier_children DROP FOREIGN KEY FK_641FB35F82E2CF35');
        $this->addSql('ALTER TABLE atelier_children DROP FOREIGN KEY FK_641FB35F3D3D2749');
        $this->addSql('DROP TABLE atelier_children');
    }
}
