<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210210161509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concession ADD nearest_car_body_workshop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE concession ADD CONSTRAINT FK_B517BD9DEF5BEE12 FOREIGN KEY (nearest_car_body_workshop_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_B517BD9DEF5BEE12 ON concession (nearest_car_body_workshop_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE concession DROP FOREIGN KEY FK_B517BD9DEF5BEE12');
        $this->addSql('DROP INDEX IDX_B517BD9DEF5BEE12 ON concession');
        $this->addSql('ALTER TABLE concession DROP nearest_car_body_workshop_id');
    }
}
