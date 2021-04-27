<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210427021839 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE delivery_date delivery_date VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD products_id INT NOT NULL, DROP product');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE66C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2530ADE66C8A81A9 ON order_product (products_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE delivery_date delivery_date VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE66C8A81A9');
        $this->addSql('DROP INDEX IDX_2530ADE66C8A81A9 ON order_product');
        $this->addSql('ALTER TABLE order_product ADD product LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', DROP products_id');
    }
}
