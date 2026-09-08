<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260905094428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create accounts table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE accounts (
            id CHAR(36) NOT NULL,
            type ENUM(\'PERSONAL\', \'BUSINESS\', \'CLUB\', \'SUPPLIER\', \'PARTNER\') NOT NULL,
            username VARCHAR(24) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            verified BOOLEAN NOT NULL,
            referred_by CHAR(36) DEFAULT NULL,
            email_marketing_accepted BOOLEAN NOT NULL,
            terms_accepted BOOLEAN NOT NULL,
            terms_accepted_version INT NOT NULL,
            terms_accepted_at DATETIME NOT NULL,
            activation_token VARCHAR(255) DEFAULT NULL,
            activation_token_expires_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL,
            activated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_ACCOUNTS_USERNAME ON accounts (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ACCOUNTS_EMAIL ON accounts (email)');
        $this->addSql('CREATE INDEX idx_user_credentials_username ON accounts (username)');
        $this->addSql('CREATE INDEX idx_user_credentials_email ON accounts (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE accounts');

    }

    public function isTransactional(): bool
    {
        return true;
    }
}
