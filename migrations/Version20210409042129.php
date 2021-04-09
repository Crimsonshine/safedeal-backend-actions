<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409042129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD customer_id INT NOT NULL, ADD sender_id INT NOT NULL, DROP customer, DROP sender, CHANGE courier courier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398E3D8151C FOREIGN KEY (courier_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993989395C3F3 ON `order` (customer_id)');
        $this->addSql('CREATE INDEX IDX_F5299398F624B39D ON `order` (sender_id)');
        $this->addSql('CREATE INDEX IDX_F5299398E3D8151C ON `order` (courier_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F624B39D');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398E3D8151C');
        $this->addSql('DROP INDEX IDX_F52993989395C3F3 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398F624B39D ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398E3D8151C ON `order`');
        $this->addSql('ALTER TABLE `order` ADD customer INT NOT NULL, ADD sender INT NOT NULL, DROP customer_id, DROP sender_id, CHANGE courier_id courier INT DEFAULT NULL');
    }
}
