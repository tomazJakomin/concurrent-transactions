<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423134407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            ALTER TABLE transactions CHANGE status status VARCHAR(20) DEFAULT 'processed' NOT NULL
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_EAA81A4C2FC0CB0F ON transactions (transaction_id)
        ");
        $this->addSql("
            ALTER TABLE users CHANGE balance balance NUMERIC(10, 2) DEFAULT '100' NOT NULL
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("
            DROP INDEX UNIQ_EAA81A4C2FC0CB0F ON transactions
        ");
        $this->addSql("
            ALTER TABLE transactions CHANGE status status VARCHAR(20) NOT NULL
        ");
        $this->addSql("
            DROP INDEX UNIQ_1483A5E9F85E0677 ON users
        ");
        $this->addSql("
            DROP INDEX UNIQ_1483A5E9E7927C74 ON users
        ");
        $this->addSql("
            ALTER TABLE users CHANGE balance balance NUMERIC(10, 2) NOT NULL
        ");
    }
}
