<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250927100255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Base tables for Finance and Core';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE core_warnings (id INT AUTO_INCREMENT NOT NULL, object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, category VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE finance_budgets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, balance DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE finance_contractors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE finance_invoices (id INT AUTO_INCREMENT NOT NULL, contractor_id INT NOT NULL, number VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, is_paid TINYINT(1) NOT NULL, due_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_BB79DA8896901F54 (number), INDEX IDX_BB79DA88B0265DC7 (contractor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE finance_invoices ADD CONSTRAINT FK_BB79DA88B0265DC7 FOREIGN KEY (contractor_id) REFERENCES finance_contractors (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE finance_invoices DROP FOREIGN KEY FK_BB79DA88B0265DC7');
        $this->addSql('DROP TABLE core_warnings');
        $this->addSql('DROP TABLE finance_budgets');
        $this->addSql('DROP TABLE finance_contractors');
        $this->addSql('DROP TABLE finance_invoices');
    }
}
