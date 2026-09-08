<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260905124957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create sessions and refresh_tokens tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE sessions (
                id CHAR(36) NOT NULL,
                account_id CHAR(36) NOT NULL,
                device VARCHAR(255) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                INDEX idx_sessions_account_id (account_id),
                INDEX idx_sessions_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $this->addSql('
            CREATE TABLE refresh_tokens (
                id CHAR(36) NOT NULL,
                session_id CHAR(36) NOT NULL,
                hashed_token VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                revoked_at DATETIME DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE INDEX uniq_refresh_tokens_hashed_token (hashed_token),
                INDEX idx_refresh_tokens_session_id (session_id),
                INDEX idx_refresh_tokens_cleanup (expires_at, revoked_at),
                CONSTRAINT fk_refresh_tokens_session
                    FOREIGN KEY (session_id)
                    REFERENCES sessions (id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE sessions');
    }

    public function isTransactional(): bool
    {
        return true;
    }
}
