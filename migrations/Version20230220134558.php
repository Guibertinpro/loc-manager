<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220134558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP INDEX UNIQ_42C849555D83CC1, ADD INDEX IDX_42C849555D83CC1 (state_id)');
        $this->addSql('ALTER TABLE reservation DROP INDEX UNIQ_42C84955176DFE85, ADD INDEX IDX_42C84955176DFE85 (apartment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP INDEX IDX_42C84955176DFE85, ADD UNIQUE INDEX UNIQ_42C84955176DFE85 (apartment_id)');
        $this->addSql('ALTER TABLE reservation DROP INDEX IDX_42C849555D83CC1, ADD UNIQUE INDEX UNIQ_42C849555D83CC1 (state_id)');
    }
}
