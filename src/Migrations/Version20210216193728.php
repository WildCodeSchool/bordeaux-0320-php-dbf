<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216193728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dbf_contact (id INT AUTO_INCREMENT NOT NULL, civility_id INT NOT NULL, service_id INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, immat VARCHAR(255) NOT NULL, date DATE NOT NULL, hour INT NOT NULL, minute VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_2CE9A01923D6A298 (civility_id), INDEX IDX_2CE9A019ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dbf_contact ADD CONSTRAINT FK_2CE9A01923D6A298 FOREIGN KEY (civility_id) REFERENCES civility (id)');
        $this->addSql('ALTER TABLE dbf_contact ADD CONSTRAINT FK_2CE9A019ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE dbf_contact');
    }
}
