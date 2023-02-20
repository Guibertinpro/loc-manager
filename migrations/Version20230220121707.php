<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220121707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP INDEX state_id, ADD UNIQUE INDEX UNIQ_42C849555D83CC1 (state_id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849555D83CC1 FOREIGN KEY (state_id) REFERENCES reservation_state (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP INDEX UNIQ_42C849555D83CC1, ADD INDEX state_id (state_id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849555D83CC1');
    }
}
