<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423132742 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql(
			"
            CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, transaction_id VARCHAR(255) NOT NULL, bet_amount NUMERIC(10, 2) NOT NULL, game_type VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_EAA81A4C9D86650F (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
		);
		$this->addSql(
			"
            CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
		);
		$this->addSql(
			"ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C9D86650F FOREIGN KEY (user_id) REFERENCES users (id)"
		);
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql(
			"ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C9D86650F"
		);
		$this->addSql(
			"DROP TABLE transactions"
		);
		$this->addSql(
			"DROP TABLE users"
		);
	}
}
