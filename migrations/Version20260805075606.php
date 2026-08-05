<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805075606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Géographie';
    }

    public function up(Schema $schema): void
    {
        /*
        -- Le module pg_trgm fournit des fonctions et des opérateurs pour déterminer la
        -- similitude du texte alphanumérique ASCII basé sur la correspondance de
        -- trigramme, ainsi que des classes d'opérateurs d'index qui prennent en charge
        -- la recherche rapide de chaînes similaires.
        */
        $this->addSql('create extension if not exists pg_trgm');

        /*
        -- unaccent est un dictionnaire de recherche plein texte qui supprime les accents d'un lexeme.
        */
        $this->addSql('create extension if not exists unaccent');

        $this->addSql(<<<SQL
        create or replace function norm(text)
        returns text
        language sql
        stable
        as $$
            select unaccent(lower($1))
        $$;
        SQL);

        $this->addSql(<<<SQL
        create table tncc (
            id smallint primary key,
            article text not null,
            charniere text not null
        )
        SQL);

        $this->addSql(<<<SQL
        insert into tncc values
        (0, '', 'de '),
        (1, '', 'd'''),
        (2, 'le ', 'du '),
        (3, 'la ', 'de la '),
        (4, 'les ', 'des '),
        (5, 'l''', 'de l'''),
        (6, 'aux ', 'des '),
        (7, 'las ', 'de las '),
        (8, 'los ', 'de los ')
        SQL);

        $this->addSql(<<<SQL
        create table country (
            code text primary key,
            country text not null,
            tncc_id smallint references tncc not null,
            flag text,
            "long" text,
            intracommunity boolean not null default false,
            sepa boolean not null default false,
            phone_code smallint
        )
        SQL);

        $this->addSql('create index idx_country_country_code on country (country, code)');

        $this->addSql('alter table country add column _country text not null');

        $this->addSql(<<<SQL
        create or replace function country_search_text_trigger()
        returns trigger
        language plpgsql
        as $$
        begin
            new._country := concat(norm(new.country), ' ', norm(new.long));
            return new;
        end;
        $$
        SQL);

        $this->addSql(<<<SQL
        create trigger trg_country_search_text
        before insert or update of country
        on country
        for each row
        execute function country_search_text_trigger()
        SQL);

        $this->addSql(<<<SQL
        create index idx_country_search
        on country
        using gin (_country gin_trgm_ops)
        SQL);

        $this->addSql(<<<SQL
        create temporary table pays_import
        (
            code2 text,
            code3 text,
            code_num text,
            pays text,
            tncc smallint,
            drapeau_unicode text,
            forme_longue text,
            independant boolean,
            communautaire boolean,
            sepa boolean,
            telephone smallint
        )
        SQL);

        $this->addSql('copy pays_import from \'/data/app/geo/pays.csv\' (format csv, header)');

        $this->addSql(<<<SQL
        insert into country
        select
            code2,
            pays,
            tncc,
            drapeau_unicode,
            forme_longue,
            communautaire,
            sepa,
            telephone
        from pays_import
        where independant is true
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table country');
        $this->addSql('drop table tncc');
    }
}
