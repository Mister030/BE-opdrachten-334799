# BE-opdracht 01 — Realiseren van User Stories (Jamin)

Laravel-applicatie voor het bedrijf Jamin. Klas IO-SD-2509, studentnummer 334799.

## Gerealiseerde user stories

### User story 01 — Inzien leveringsinformatie product
- **Scenario_01** — via het vraagteken-icoon in de kolom *Leverantie Info* opent het
  detailscherm *Levering Informatie* met de leveranciergegevens boven de tabel en
  alle leveringen, gesorteerd op datum laatste levering oplopend.
- **Scenario_02** — is er geen voorraad van het product, dan staan de
  leveranciergegevens nog steeds boven de tabel en toont de tabel de melding dat er
  geen voorraad aanwezig is met de verwachte eerstvolgende leveringsdatum, waarna na
  4 seconden wordt doorverwezen naar *Overzicht Magazijn Jamin*.

### User story 02 — Inzien allergeneninformatie van product
- **Scenario_01** — via het rode kruis-icoon in de kolom *Allergenen Info* opent het
  detailscherm *Overzicht Allergenen* met naam en barcode boven de tabel en alle
  allergenen, gesorteerd op naam oplopend.
- **Scenario_02** — bevat het product geen allergenen, dan toont de tabel de melding
  dat er geen allergene stoffen in zitten, waarna na 4 seconden wordt doorverwezen
  naar *Overzicht Magazijn Jamin*.

## Techniek
- MVC via Laravel, OOP in de model- en controllerclasses.
- Alle databasequeries draaien via PDO (`DB::select` / `DB::selectOne`) met named
  binding, zodat SQL-injectie niet mogelijk is. Geen Eloquent voor de Jamin-tabellen.
- Codeconventie PSR-12.

## Database
Alles staat in één database `mvc`: de zes specificatietabellen van de opdracht
(`Allergeen`, `Leverancier`, `Magazijn`, `Product`, `ProductPerAllergeen`,
`ProductPerLeverancier`) en de tabellen van Laravel zelf (`users`, `sessions`,
`cache`, `migrations`, ...).

Het createscript `database/migrations/create_script_jamin_1.sql` wordt uitgevoerd door
de migratie `0001_01_01_000003_create_Import_database_jamin.php`. Het script maakt alleen
de zes Jamin-tabellen (opnieuw) aan, zodat de tabellen van Laravel in dezelfde database
blijven staan.

## Mappen
| Map | Inhoud |
|---|---|
| `database/migrations/` | Migraties en het createscript van de Jamin-tabellen |
| `db/` | SQL-script van de database `mvc` (zelfde script als in `database/migrations/`) |
| `docs/` | Database Specificatie Tabel |
| `vids/` | Schermopname van de gerealiseerde scenario's |

## Installeren
```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
```

Zet in `.env`:
```
DB_CONNECTION=mysql
DB_DATABASE=mvc
DB_USERNAME=root
DB_PASSWORD=
```

Database aanmaken en starten:
```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

> Het createscript kan ook los gedraaid worden (bijvoorbeeld in phpMyAdmin). Het maakt
> dan alleen de zes Jamin-tabellen opnieuw aan; de gebruikers en de andere tabellen van
> Laravel blijven staan.

## Testgebruikers
| E-mail | Rol |
|---|---|
| magazijnmedewerker@jamin.nl | magazijnmedewerker |
| inkoper@jamin.nl | inkoper |
| magazijnbeheerder@jamin.nl | magazijnbeheerder |

Wachtwoord van alle drie: `password`.

## Tests
```bash
php artisan test tests/Feature/Jamin
```
De tests draaien tegen de MySQL-database `mvc` en controleren per scenario de
getoonde gegevens, de sortering en de doorverwijzing na 4 seconden.
