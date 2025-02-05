<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205053231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD is_homepage TINYINT(1) NOT NULL, CHANGE event_price_no_food event_price_no_food VARCHAR(255) NOT NULL, CHANGE event_price_with_food event_price_with_food VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP is_homepage, CHANGE event_price_no_food event_price_no_food DOUBLE PRECISION NOT NULL, CHANGE event_price_with_food event_price_with_food DOUBLE PRECISION NOT NULL');
    }
}
