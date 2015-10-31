---
title: Doctrine Migrations v Nette
categories:
    - Doctrine
    - Nette
---

Kromě uprchlíků v dnešní době migruje i naše databáze. Jako pohodolní programátoři celý problém migrací můžeme vyřešit pomocí jednoho balíčku.

Stojí nám ta namáha vůbec za to?

## Kdy ti migrace ušetří práci

- na projektu nepracuješ sám
- tvé entity se občas mění
- používáš příkazovou řádku
- potřebuješ aplikaci naplnit základními daty
- základní data se občas mění: tu přidat jazyk, tu sazbu DPH..

Pokud se tě to týká, mám pro tebe řešení! Doctrine Migrations!

Stejně jako [Kdyby\Doctrine](https://github.com/Kdyby/Doctrine) integruje originální [doctrine/doctrine2](https://github.com/doctrine/doctrine2), tento balíček integruje **[doctrine/migrations](https://github.com/doctrine/migrations)**.

Ten si denně [stáhne přes 8 000 programátorů](https://packagist.org/packages/doctrine/migrations/stats), kteří potřebují, aby balíček opravdu fungoval.


## Instalace ve 2 krocích

*Celý postup si můžete prohlédnout v commitech [repositáře na Githubu](https://github.com/TomasVotruba/doctrine-migrations-sandbox/commits/master)*.

Balíček nainstalujeme přes `composer`:

```sh
composer require zenify/doctrine-migrations
```

My jen přidáme rozšíření do `config.neon`:

```yaml
extensions:
    migrations: Zenify\DoctrineMigrations\DI\MigrationsExtension
    - Symnedi\EventDIspatcher\DI\EventDispatcherExtension

    # nesmí chybět Kdyby\Doctrine nebo jiná integrace Doctrine
```


## Zkouška spojení

Že vše proběhlo v pořádku, prověříme spuštěním z přikažového řádku. V Nette to znamená využít `Kdyby\Console`.

Zkusíme tedy vypsat všechny příkazy týkající se migrací.

```bash
php www/index.php list migrations
```

```bash
# ...

Available commands for the "migrations" namespace:
  migrations:diff      Generate a migration by comparing your current database to your mapping information.
  migrations:execute   Execute a single migration version up or down manually.
  migrations:generate  Generate a blank migration class.
  migrations:migrate   Execute a migration to a specified version or the latest available version.
  migrations:status    View the status of a set of migrations.
  migrations:version   Manually add and delete migration versions from the version table.
```

Pokud vidíme přehled příkazů, máme vyhráno a můžeme používat.


---


## Profi workflow ve 4 krocích

V [Lékárně.cz](http://lekarna.cz/) používáme jednoduchý proces, který pokrývá 90 % případů. Tady je.

*Od šéfa jsme dostali zadání: vytvořit tabulku na články.* To je výzva, tak jdem na to!

### 1. Zkontrolujeme status

```sh
php www/index.php migrations:status
```

```bash
 == Configuration

    >> Name:                                               Doctrine Database Migrations
    >> Database Driver:                                    pdo_sqlite
    >> Database Name:                                      
    >> Configuration Source:                               manually configured
    >> Version Table Name:                                 doctrine_migrations
    >> Migrations Namespace:                               Migrations
    >> Migrations Directory:                               /var/www/tomas-votruba-doctrine-migrations-sandbox/app/../migrations
    >> Previous Version:                                   Already at first version
    >> Current Version:                                    0
    >> Next Version:                                       Already at latest version
    >> Latest Version:                                     0
    >> Executed Migrations:                                0
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               0
    >> New Migrations:                                     0
```

Důležitá jsou poslední 2 čísla. Vypadá to, že vše je aktuální, tak můžeme pokračovat.

### 2. Vytvoříme si novou prázdnou migraci

```sh
php www/index.php migrations:generate
```

```bash
Loading configuration from the integration code of your framework (setter).
Generated new migration class to "/var/www/tomas-votruba-doctrine-migrations-sandbox/app/../migrations/Version20151031185405.php"
```

Název migrace je generován automaticky dle timestampu. Tady je použita defaultní složka `/migrations`. Mrknem tam!


### 3. Přidáme vlastní SQL

- `up` je použita defaultně, při migraci na novější verzi
- `down` pak migruje zpět na starší verzi; používá se zřídka, obvykle při debugování jiné než aktuální verze aplikace

Více bude jasnější ze samotného SQL zápisu:

~~~language-php
namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151031185405 extends AbstractMigration
{

	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->addSql('CREATE TABLE "article" ("id" serial NOT NULL, "name" text NOT NULL);');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->addSql('DROP TABLE "article";');
	}

}
~~~

A je to!

Když znovu dáme znovu status, uvidíme, že máme jednu novou migraci.

```
php www/index.php migrations:status
```

```bash
# ...

    >> Executed Migrations:                                0
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               1
    >> New Migrations:                                     1
```


Ukáže se nám mezi "New migrations" právě díky tomu, že nebyla aplikována na databázi.


### 4. Teď si konečně sáhneme na databázi!

Aplikujeme všechny nové změny:

```sh
php www/index.php migrations:migrate
```

```sh
Loading configuration from the integration code of your framework (setter).
                                                                    
                    Doctrine Database Migrations                    
                                                                    

WARNING! You are about to execute a database migration that could result in schema changes and data lost. Are you sure you wish to continue? (y/n)
```

Potvrdíme, že cheme opravdu migrovat: "y"


```sh
Migrating up to 20151031185555 from 0

  ++ migrating 20151031185555

     -> CREATE TABLE "article" ("id" serial NOT NULL, "name" text NOT NULL);

  ++ migrated (0.02s)

  ------------------------

  ++ finished in 0.02
  ++ 1 migrations executed
  ++ 1 sql queries
```


A je to! Gratuluju, jste připraveni migrovat!

![Skvělá práce!](/../../../../images/posts/2015/09/15/7-success-meme.jpg)
