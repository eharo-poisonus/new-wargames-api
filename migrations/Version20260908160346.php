<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260908160346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add terminated_at to sessions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE sessions
                ADD terminated_at DATETIME DEFAULT NULL,
                ADD INDEX idx_sessions_active (account_id, terminated_at)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE sessions
                DROP INDEX idx_sessions_active,
                DROP COLUMN terminated_at
        ');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
