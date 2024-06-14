<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614102434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, stay_id INT NOT NULL, title VARCHAR(255) NOT NULL, date DATE NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_480D45C2FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, stay_id INT NOT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, medication JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_1FBFB8D9FB3AF7D6 (stay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stay (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, doctor_id INT NOT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, reason VARCHAR(255) NOT NULL, INDEX IDX_5E09839CA76ED395 (user_id), INDEX IDX_5E09839C87F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, specialty_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, registration_number INT DEFAULT NULL, INDEX IDX_8D93D6499A353316 (specialty_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stay (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9FB3AF7D6 FOREIGN KEY (stay_id) REFERENCES stay (id)');
        $this->addSql('ALTER TABLE stay ADD CONSTRAINT FK_5E09839CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stay ADD CONSTRAINT FK_5E09839C87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2FB3AF7D6');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9FB3AF7D6');
        $this->addSql('ALTER TABLE stay DROP FOREIGN KEY FK_5E09839CA76ED395');
        $this->addSql('ALTER TABLE stay DROP FOREIGN KEY FK_5E09839C87F4FB17');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499A353316');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE stay');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
