# Økonomi

Personlig regnskapsapp. Livewire: innboks for å kategorisere, manuell registrering, CSV-import, oversikt, budsjett, formue, investeringer, faste kostnader og bolig. Forslag vises, men settes aldri automatisk.

## Lokalt

Krever PHP 8.3+, Composer og Node.

```bash
composer setup
php artisan migrate
php artisan serve
```

Standard database er SQLite (`database/database.sqlite`). Registrer deg på `/register` — det er meningen, også i skyen.

Etter innlogging lander du i **Innboks** (`/inbox`). Én transaksjon om gangen: velg kategori, hopp over, eller del opp i to. **Ny**, **Import**, **Oversikt** og de andre sidene ligger i menyen.

Bank-CSV uten `kategori` blir ukategorisert. En ledger-CSV med `dato,belop,kategori,brukersted,betalingsmate,notat` beholder kategori (ukjente navn blir tomme).

## Laravel Cloud

1. Opprett et GitHub-repo og push denne koden (ikke commit `.env`).
2. Gå til [cloud.laravel.com](https://cloud.laravel.com), opprett en app og koble GitHub-repoet.
3. Legg til **Serverless Postgres**. Cloud setter `DB_*` selv — du trenger ikke lime inn passord i `.env.example`.
4. Slå på **hibernation** (scale to zero) på appen og databasen, så du ikke betaler når den sover.
5. Deploy. Første gang: åpne appen, registrer deg som første bruker, og importer CSV fra Innboks → Import.

Migrasjonene er portable (ingen SQLite-spesifikke kolonner), så samme skjema kjører mot MySQL eller Postgres.

## Utvikling

```bash
php artisan test
```
