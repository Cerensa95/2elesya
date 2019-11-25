<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125202551 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments CHANGE oyun_id oyun_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE shipinfo shipinfo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sales ADD image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE shop_cart CHANGE userid userid INT DEFAULT NULL, CHANGE productid productid INT DEFAULT NULL, CHANGE quantity quantity INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments CHANGE oyun_id oyun_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE shipinfo shipinfo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sales DROP image');
        $this->addSql('ALTER TABLE shop_cart CHANGE userid userid INT DEFAULT NULL, CHANGE productid productid INT DEFAULT NULL, CHANGE quantity quantity INT DEFAULT NULL');
    }
}
