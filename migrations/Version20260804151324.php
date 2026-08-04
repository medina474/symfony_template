<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260701130458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Chronologie';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        create table chronologie as
        with recursive calendrier as (
            select '2023-01-01 00:00:00'::timestamp as jour
            union all
            select jour + interval '1 day'
            from calendrier
            where jour + interval '1 day' <= '2027-12-31'
        )

        select
            (extract(epoch from jour) / 86400::int + 2440588)::int as jj,
            extract(epoch from jour)::bigint as epoch,
            jour::date,
            extract(year from jour)::smallint as annee,
            extract(month from jour)::smallint as mois,
            extract(day from jour)::smallint as jmois,
            extract(week from jour)::smallint as semaine,
            extract(dow from jour)::smallint as jsemaine,
            extract(doy from jour)::smallint as jannee,
            (floor((extract(month from jour) - 1) / 6) + 1)::smallint as semestre,
            (floor((extract(month from jour) - 1) / 4) + 1)::smallint as quadrimestre,
            extract(quarter from jour)::smallint as trimestre,
            (floor((extract(month from jour) - 1) / 2) + 1)::smallint as bimestre,
            (extract(day from jour) / extract(day from (date_trunc('month', '2025-03-16'::date) + interval '1 month' - interval '1 day')))::double precision as frac_mois,
            (extract(doy from jour) / extract(doy from (extract(year from jour) || '-12-31')::date))::double precision as frac_annee
        from calendrier;
        SQL);

        $this->addSql('alter table chronologie add primary key (jj)');
        $this->addSql('comment on column chronologie.jj is \'Jour julien\'');
        $this->addSql('alter table chronologie alter jj set default 2440588;');
        $this->addSql('alter table chronologie alter jj set not null;');
        $this->addSql('alter table chronologie alter epoch set default 0;');
        $this->addSql('alter table chronologie alter epoch set not null;');
        $this->addSql('alter table chronologie alter jour set default \'1970-01-01\'');
        $this->addSql('alter table chronologie alter jour set not null');
        $this->addSql('alter table chronologie alter jmois set default 0');
        $this->addSql('alter table chronologie alter jmois set not null');
        $this->addSql('alter table chronologie alter annee set default 0');
        $this->addSql('alter table chronologie alter annee set not null');
        $this->addSql('alter table chronologie alter mois set default 0');
        $this->addSql('alter table chronologie alter mois set not null');
        $this->addSql('alter table chronologie alter semestre set default 0');
        $this->addSql('alter table chronologie alter semestre set not null');
        $this->addSql('alter table chronologie alter quadrimestre set default 0');
        $this->addSql('alter table chronologie alter quadrimestre set not null');
        $this->addSql('alter table chronologie alter trimestre set default 0');
        $this->addSql('alter table chronologie alter trimestre set not null');
        $this->addSql('alter table chronologie alter bimestre set default 0');
        $this->addSql('alter table chronologie alter bimestre set not null');
        $this->addSql('alter table chronologie alter semaine set default 0');
        $this->addSql('alter table chronologie alter semaine set not null');
        $this->addSql('alter table chronologie alter jsemaine set default 0');
        $this->addSql('alter table chronologie alter jsemaine set not null');
        $this->addSql('alter table chronologie alter jmois set default 0');
        $this->addSql('alter table chronologie alter jmois set not null');
        $this->addSql('alter table chronologie alter jannee set default 0');
        $this->addSql('alter table chronologie alter jannee set not null');
        $this->addSql('alter table chronologie alter frac_mois set default 0');
        $this->addSql('alter table chronologie alter frac_mois set not null');
        $this->addSql('alter table chronologie alter frac_annee set default 0');
        $this->addSql('alter table chronologie alter frac_annee set not null');

        $this->addSql(<<<SQL
        create view reporting.chronologie as
        select
            jj,
            jour,
            annee,
            mois,
            jmois,
            semaine,
            jsemaine,
            jannee,
            semestre,
            quadrimestre,
            trimestre,
            bimestre,
            frac_mois,
            frac_annee
        from public.chronologie
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop view reporting.chronologie');
        $this->addSql('drop table chronologie');
    }
}
