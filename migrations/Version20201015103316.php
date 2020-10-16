<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015103316 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD restaurateur_id INT NOT NULL, ADD fournisseur_id INT NOT NULL, DROP societe');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D3B311E56 FOREIGN KEY (restaurateur_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D670C757F FOREIGN KEY (fournisseur_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D3B311E56 ON commande (restaurateur_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D670C757F ON commande (fournisseur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D3B311E56');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D670C757F');
        $this->addSql('DROP INDEX IDX_6EEAA67D3B311E56 ON commande');
        $this->addSql('DROP INDEX IDX_6EEAA67D670C757F ON commande');
        $this->addSql('ALTER TABLE commande ADD societe VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP restaurateur_id, DROP fournisseur_id');
    }
}
