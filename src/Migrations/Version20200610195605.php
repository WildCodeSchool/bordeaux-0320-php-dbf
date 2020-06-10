<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200610195605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `call` CHANGE is_process_ended is_process_ended TINYINT(1) DEFAULT NULL, CHANGE is_appointment_taken is_appointment_taken TINYINT(1) DEFAULT NULL, CHANGE is_processed is_processed TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `call` CHANGE is_process_ended is_process_ended TINYINT(1) NOT NULL, CHANGE is_appointment_taken is_appointment_taken TINYINT(1) NOT NULL, CHANGE is_processed is_processed TINYINT(1) NOT NULL');
    }
}
