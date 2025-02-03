<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203080114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog_video DROP FOREIGN KEY FK_C5D4BB9529C1004E');
        $this->addSql('ALTER TABLE catalog_video DROP FOREIGN KEY FK_C5D4BB95CC3C66FC');
        $this->addSql('DROP TABLE catalog_video');
        $this->addSql('ALTER TABLE catalog DROP video_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE catalog_video (catalog_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_C5D4BB95CC3C66FC (catalog_id), INDEX IDX_C5D4BB9529C1004E (video_id), PRIMARY KEY(catalog_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE catalog_video ADD CONSTRAINT FK_C5D4BB9529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog_video ADD CONSTRAINT FK_C5D4BB95CC3C66FC FOREIGN KEY (catalog_id) REFERENCES catalog (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE catalog ADD video_id INT NOT NULL');
    }
}
