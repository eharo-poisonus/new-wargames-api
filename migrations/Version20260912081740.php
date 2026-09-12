<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260912081740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create players table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS players (
                id CHAR(36) NOT NULL PRIMARY KEY,
                account_id CHAR(36) NOT NULL,
                username VARCHAR(30) NOT NULL UNIQUE,
                avatar_url VARCHAR(255) DEFAULT NULL,
                cover_url VARCHAR(255) DEFAULT NULL,
                bio TEXT DEFAULT NULL,
                gender ENUM(\'MALE\', \'FEMALE\', \'OTHER\', \'UNKNOWN\') DEFAULT NULL,
                date_of_birth DATE DEFAULT NULL,
                country VARCHAR(2) DEFAULT NULL,
                state VARCHAR(255) DEFAULT NULL,
                city VARCHAR(255) DEFAULT NULL,
                zip_code VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL
            )
        ');

        $this->addSql('CREATE INDEX idx_players_account_id ON players (account_id)');
        $this->addSql('CREATE INDEX idx_players_username ON players (username)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS players');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
