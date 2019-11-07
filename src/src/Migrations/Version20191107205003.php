<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107205003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE basic_data (id INT AUTO_INCREMENT NOT NULL, zipcode_id INT NOT NULL, avg DOUBLE PRECISION NOT NULL, cards INT NOT NULL, date DATETIME NOT NULL, txs INT NOT NULL, merchants INT NOT NULL, min DOUBLE PRECISION NOT NULL, peak_txs_day INT NOT NULL, peak_txs_hour INT NOT NULL, std DOUBLE PRECISION NOT NULL, valley_txs_day INT NOT NULL, valley_txs_hour INT NOT NULL, max DOUBLE PRECISION NOT NULL, INDEX IDX_F9E5BF08E4C7FA21 (zipcode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basic_data ADD CONSTRAINT FK_F9E5BF08E4C7FA21 FOREIGN KEY (zipcode_id) REFERENCES zipcode (id)');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE basic_data');
        $this->addSql('ALTER TABLE sub_category CHANGE category_id category_id INT DEFAULT NULL');
    }
}
