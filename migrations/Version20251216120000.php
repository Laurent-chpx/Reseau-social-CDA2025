<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251216120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add phone, accountType and biography fields to User entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD account_type VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD biography LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP phone');
        $this->addSql('ALTER TABLE user DROP account_type');
        $this->addSql('ALTER TABLE user DROP biography');
    }
}