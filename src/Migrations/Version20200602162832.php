<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200602162832 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE right_by_location (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_id INT DEFAULT NULL, concession_id INT DEFAULT NULL, city_id INT DEFAULT NULL, authorization_id INT NOT NULL, INDEX IDX_5BF77D77A76ED395 (user_id), INDEX IDX_5BF77D77ED5CA9E6 (service_id), INDEX IDX_5BF77D774132BB14 (concession_id), INDEX IDX_5BF77D778BAC62AF (city_id), INDEX IDX_5BF77D772F8B0EB2 (authorization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE civility (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE concession (id INT AUTO_INCREMENT NOT NULL, town_id INT NOT NULL, name VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, postcode INT NOT NULL, city VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_B517BD9D75E23604 (town_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, concession_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD24132BB14 (concession_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(155) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_transfer (id INT AUTO_INCREMENT NOT NULL, by_whom_id INT NOT NULL, from_whom_id INT NOT NULL, to_whom_id INT NOT NULL, refered_call_id INT NOT NULL, created_at DATETIME NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_1652C27A60D8982D (by_whom_id), INDEX IDX_1652C27A9D748C23 (from_whom_id), INDEX IDX_1652C27A95B25279 (to_whom_id), INDEX IDX_1652C27ABE5DC693 (refered_call_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `call` (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, vehicle_id INT NOT NULL, subject_id INT NOT NULL, comment_id INT DEFAULT NULL, service_id INT NOT NULL, user_id INT NOT NULL, recall_period_id INT NOT NULL, is_urgent TINYINT(1) NOT NULL, is_process_ended TINYINT(1) NOT NULL, is_appointment_taken TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, recall_date DATETIME NOT NULL, free_comment VARCHAR(255) DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, internet VARCHAR(255) DEFAULT NULL, is_processed TINYINT(1) NOT NULL, INDEX IDX_CC8E2F3E19EB6921 (client_id), INDEX IDX_CC8E2F3E545317D1 (vehicle_id), INDEX IDX_CC8E2F3E23EDC87 (subject_id), INDEX IDX_CC8E2F3EF8697D13 (comment_id), INDEX IDX_CC8E2F3EED5CA9E6 (service_id), INDEX IDX_CC8E2F3EA76ED395 (user_id), INDEX IDX_CC8E2F3ED31619AD (recall_period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_user (call_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA12B11550A89B2C (call_id), INDEX IDX_BA12B115A76ED395 (user_id), PRIMARY KEY(call_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recall_period (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE call_processing (id INT AUTO_INCREMENT NOT NULL, contact_type_id INT NOT NULL, refered_call_id INT NOT NULL, created_at DATETIME NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_ACF3EAB15F63AD12 (contact_type_id), INDEX IDX_ACF3EAB1BE5DC693 (refered_call_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, immatriculation VARCHAR(15) NOT NULL, chassis VARCHAR(255) DEFAULT NULL, has_come VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1B80E48619EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `right` (id INT AUTO_INCREMENT NOT NULL, level VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, civility_id INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, phone2 VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, postcode INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C744045523D6A298 (civility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_for_app_workshop TINYINT(1) NOT NULL, INDEX IDX_FBCE3E7A8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE right_by_location ADD CONSTRAINT FK_5BF77D77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE right_by_location ADD CONSTRAINT FK_5BF77D77ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE right_by_location ADD CONSTRAINT FK_5BF77D774132BB14 FOREIGN KEY (concession_id) REFERENCES concession (id)');
        $this->addSql('ALTER TABLE right_by_location ADD CONSTRAINT FK_5BF77D778BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE right_by_location ADD CONSTRAINT FK_5BF77D772F8B0EB2 FOREIGN KEY (authorization_id) REFERENCES `right` (id)');
        $this->addSql('ALTER TABLE concession ADD CONSTRAINT FK_B517BD9D75E23604 FOREIGN KEY (town_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD24132BB14 FOREIGN KEY (concession_id) REFERENCES concession (id)');
        $this->addSql('ALTER TABLE call_transfer ADD CONSTRAINT FK_1652C27A60D8982D FOREIGN KEY (by_whom_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE call_transfer ADD CONSTRAINT FK_1652C27A9D748C23 FOREIGN KEY (from_whom_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE call_transfer ADD CONSTRAINT FK_1652C27A95B25279 FOREIGN KEY (to_whom_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE call_transfer ADD CONSTRAINT FK_1652C27ABE5DC693 FOREIGN KEY (refered_call_id) REFERENCES `call` (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3E19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3E545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3ED31619AD FOREIGN KEY (recall_period_id) REFERENCES recall_period (id)');
        $this->addSql('ALTER TABLE call_user ADD CONSTRAINT FK_BA12B11550A89B2C FOREIGN KEY (call_id) REFERENCES `call` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE call_user ADD CONSTRAINT FK_BA12B115A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE call_processing ADD CONSTRAINT FK_ACF3EAB15F63AD12 FOREIGN KEY (contact_type_id) REFERENCES contact_type (id)');
        $this->addSql('ALTER TABLE call_processing ADD CONSTRAINT FK_ACF3EAB1BE5DC693 FOREIGN KEY (refered_call_id) REFERENCES `call` (id)');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E48619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045523D6A298 FOREIGN KEY (civility_id) REFERENCES civility (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C744045523D6A298');
        $this->addSql('ALTER TABLE right_by_location DROP FOREIGN KEY FK_5BF77D774132BB14');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD24132BB14');
        $this->addSql('ALTER TABLE right_by_location DROP FOREIGN KEY FK_5BF77D77ED5CA9E6');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EED5CA9E6');
        $this->addSql('ALTER TABLE right_by_location DROP FOREIGN KEY FK_5BF77D778BAC62AF');
        $this->addSql('ALTER TABLE concession DROP FOREIGN KEY FK_B517BD9D75E23604');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A8BAC62AF');
        $this->addSql('ALTER TABLE call_processing DROP FOREIGN KEY FK_ACF3EAB15F63AD12');
        $this->addSql('ALTER TABLE right_by_location DROP FOREIGN KEY FK_5BF77D77A76ED395');
        $this->addSql('ALTER TABLE call_transfer DROP FOREIGN KEY FK_1652C27A60D8982D');
        $this->addSql('ALTER TABLE call_transfer DROP FOREIGN KEY FK_1652C27A9D748C23');
        $this->addSql('ALTER TABLE call_transfer DROP FOREIGN KEY FK_1652C27A95B25279');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EA76ED395');
        $this->addSql('ALTER TABLE call_user DROP FOREIGN KEY FK_BA12B115A76ED395');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EF8697D13');
        $this->addSql('ALTER TABLE call_transfer DROP FOREIGN KEY FK_1652C27ABE5DC693');
        $this->addSql('ALTER TABLE call_user DROP FOREIGN KEY FK_BA12B11550A89B2C');
        $this->addSql('ALTER TABLE call_processing DROP FOREIGN KEY FK_ACF3EAB1BE5DC693');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3ED31619AD');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3E545317D1');
        $this->addSql('ALTER TABLE right_by_location DROP FOREIGN KEY FK_5BF77D772F8B0EB2');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3E19EB6921');
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E48619EB6921');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3E23EDC87');
        $this->addSql('DROP TABLE right_by_location');
        $this->addSql('DROP TABLE civility');
        $this->addSql('DROP TABLE concession');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE contact_type');
        $this->addSql('DROP TABLE call_transfer');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE `call`');
        $this->addSql('DROP TABLE call_user');
        $this->addSql('DROP TABLE recall_period');
        $this->addSql('DROP TABLE call_processing');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE `right`');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE subject');
    }
}
