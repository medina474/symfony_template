<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805133456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Async Messenger Mercure Test (lyixx/async-messenger-mercure)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE TABLE first_name_stat (
            id UUID NOT NULL,
            gender INT NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            year_of_birth VARCHAR(255) DEFAULT NULL,
            count INT NOT NULL, PRIMARY KEY (id)
        )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table first_name_stat');
    }
}
