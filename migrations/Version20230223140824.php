<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223140824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment ADD type VARCHAR(100) NOT NULL, ADD capacity VARCHAR(10) NOT NULL, ADD surface VARCHAR(255) NOT NULL, ADD pets VARCHAR(255) NOT NULL, ADD number_of_rooms VARCHAR(255) NOT NULL, ADD number_of_beds VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP type, DROP capacity, DROP surface, DROP pets, DROP number_of_rooms, DROP number_of_beds');
    }
}
