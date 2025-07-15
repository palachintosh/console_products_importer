<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250713220549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_data (intProductDataId INT AUTO_INCREMENT NOT NULL, strProductName VARCHAR(50) NOT NULL, strProductDesc VARCHAR(255) NOT NULL, strProductCode VARCHAR(10) NOT NULL, dmtAdded DATETIME DEFAULT NULL, dmtDiscontinued DATE DEFAULT NULL, stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_201BD1C262F10A58 (strProductCode), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_data');
    }
}
