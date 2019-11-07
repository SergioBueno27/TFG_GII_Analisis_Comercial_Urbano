<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107222444 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prueba (id INT AUTO_INCREMENT NOT NULL, zipcode INT DEFAULT NULL, locality INT DEFAULT NULL, subregion INT DEFAULT NULL, region INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zipcode CHANGE zipcode zipcode INT NOT NULL, CHANGE locality locality INT NOT NULL, CHANGE subregion subregion INT NOT NULL, CHANGE region region INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prueba');
        $this->addSql('ALTER TABLE zipcode CHANGE zipcode zipcode INT DEFAULT NULL, CHANGE locality locality INT DEFAULT NULL, CHANGE subregion subregion INT DEFAULT NULL, CHANGE region region INT DEFAULT NULL');
    }
}
