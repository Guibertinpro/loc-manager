<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220100859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849553883CD9A');
        $this->addSql('DROP INDEX UNIQ_42C849553883CD9A ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE apartment_id_id apartment_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955176DFE85 ON reservation (apartment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955176DFE85');
        $this->addSql('DROP INDEX UNIQ_42C84955176DFE85 ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE apartment_id apartment_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553883CD9A FOREIGN KEY (apartment_id_id) REFERENCES apartment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849553883CD9A ON reservation (apartment_id_id)');
    }
}
