<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231025154710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Customer table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customer (
            id INT AUTO_INCREMENT NOT NULL,
            customer_id VARCHAR(5) NOT NULL, 
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            province VARCHAR(255) NOT NULL,
            cif VARCHAR(9) NOT NULL,
            PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
