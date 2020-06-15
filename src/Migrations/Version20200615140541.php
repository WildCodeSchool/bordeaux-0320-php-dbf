<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615140541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE call_user');
        $this->addSql('ALTER TABLE `call` ADD recipient_id INT DEFAULT NULL, ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CC8E2F3EE92F8F78 ON `call` (recipient_id)');
        $this->addSql('CREATE INDEX IDX_CC8E2F3EF675F31B ON `call` (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE call_user (call_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA12B11550A89B2C (call_id), INDEX IDX_BA12B115A76ED395 (user_id), PRIMARY KEY(call_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE call_user ADD CONSTRAINT FK_BA12B11550A89B2C FOREIGN KEY (call_id) REFERENCES `call` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE call_user ADD CONSTRAINT FK_BA12B115A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EE92F8F78');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EF675F31B');
        $this->addSql('DROP INDEX IDX_CC8E2F3EE92F8F78 ON `call`');
        $this->addSql('DROP INDEX IDX_CC8E2F3EF675F31B ON `call`');
        $this->addSql('ALTER TABLE `call` DROP recipient_id, DROP author_id');
    }
}
