<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251214235100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename update_at to updated_at';
    }

    public function up(Schema $schema): void
    {
        // D'abord, met à jour les valeurs NULL existantes
        $this->addSql('UPDATE event SET update_at = NOW() WHERE update_at IS NULL');

        // Ensuite, renomme la colonne
        $this->addSql('ALTER TABLE event CHANGE update_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event CHANGE updated_at update_at DATETIME NOT NULL');
    }
}
