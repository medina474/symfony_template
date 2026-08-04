<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260804152831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Job';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        create table job (
            id uuid primary key,
            "action" text not null,
            status text not null,
            payload jsonb not null,
            result jsonb null,
            error_message text null,
            retry_count smallint not null default 0,
            created_at timestamp(0) with time zone default current_timestamp not null,
            handled_at timestamp(0) with time zone default null::timestamp(0) with time zone,
            completed_at timestamp(0) with time zone default null::timestamp(0) with time zone
        )
        SQL);

        $this->addSql(<<<SQL
        create view reporting.job as
        select
            id,
            action,
            status,
            payload,
            result,
            error_message,
            retry_count,
            created_at,
            handled_at,
            completed_at
        from public.job
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop view reporting.job');
        $this->addSql('drop table job');
    }
}
