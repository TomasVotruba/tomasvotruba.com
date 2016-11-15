---
id: 1
title: Ovládni Doctrine migrace v Nette
perex: "Pokud používáte Doctrine, Nette a potřebujete měnit databázi, budou se vám hodit migrace."
thumbnail: "nette.png"
---

<p class="perex">{$perex|noescape}</p>

Stejně jako Kdyby/Doctrine využívá doctrine/doctrine2, my použijeme [doctrine/migrations](https://github.com/doctrine/migrations). Ty si [denně stáhne přes 9 000 programátorů](https://packagist.org/packages/doctrine/migrations/stats), takže se nemusíte bát o jeho kvalitu.

Do Nette jsem připravil integraci pomocí balíčku [Zenify/DoctrineMigrations](https://github.com/Zenify/DoctrineMigrations). 

## Instalace ve 3 krocích

### 1. Balíček nainstalujeme přes `composer`:

```language-bash
$ composer require zenify/doctrine-migrations
```

### 2. Přidáme rozšíření do `config.neon`:

```language-yaml
extensions:
    migrations: Zenify\DoctrineMigrations\DI\MigrationsExtension
    eventDispatcher: Symnedi\EventDIspatcher\DI\EventDispatcherExtension

    # nesmí chybět Kdyby\Doctrine nebo jiná integrace Doctrine
```

### 3. Ověříme 

Ověření provedeme spuštěním z přikazového řádku. V Nette to znamená využít Kdyby\Console.

Zkusíme vypsat všechny příkazy týkající se migrací.

```language-bash
$ php www/index.php list migrations
```

Pokud vidíme přehled příkazů, máme vyhráno a můžeme používat.

```language-bash
# ...

Available commands for the "migrations" namespace:
  migrations:diff      Generate a migration by comparing your current database to your mapping information.
  migrations:execute   Execute a single migration version up or down manually.
  migrations:generate  Generate a blank migration class.
  migrations:migrate   Execute a migration to a specified version or the latest available version.
  migrations:status    View the status of a set of migrations.
  migrations:version   Manually add and delete migration versions from the version table.
```


> Čtete raději commity? Mrkněte na [sandbox na Githubu](https://github.com/TomasVotruba/doctrine-migrations-sandbox/commits/master).


---


## Profi workflow ve 4 krocích

Rád bych s vámi podělil o to, jak přistupujeme k migracím my v [Lékárna.cz](http://lekarna.cz/). 

Od šéfa jsme dostali zadání: *vytvořit tabulku na články*.

### 1. Zkontrolujeme status

```language-bash
$ php www/index.php migrations:status
```

Důležité je číslo v posledním řádku ("New Migrations"). Vypadá to, že vše je aktuální, tak můžeme pokračovat.

```language-bash
 == Configuration

    >> Name:                                               Doctrine Database Migrations
    >> Database Driver:                                    pdo_sqlite
    >> Database Name:
    >> Configuration Source:                               manually configured
    >> Version Table Name:                                 doctrine_migrations
    >> Migrations Namespace:                               Migrations
    >> Migrations Directory:                               /var/www/doctrine-migrations-sandbox/app/../migrations
    >> Previous Version:                                   Already at first version
    >> Current Version:                                    0
    >> Next Version:                                       Already at latest version
    >> Latest Version:                                     0
    >> Executed Migrations:                                0
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               0
    >> New Migrations:                                     0
```

Pokud máme "New Migrations" větší než 0, tak je nejdříve aplikujeme ([viz krok 4.](#apply-new-migrations)). 


### 2. Vytvoříme si prázdnou migraci

```language-bash
$ php www/index.php migrations:generate
```

Název migrace je generován automaticky dle timestampu. Tady je použita defaultní složka `/migrations`.

```language-bash
Loading configuration from the integration code of your framework (setter).
Generated new migration class to "/var/www/doctrine-migrations-sandbox/app/../migrations/Version20151031185405.php"
```

### 3. Přidáme SQL příkazy

Otevřeme si novou migraci `/migrations/Version20151031185405.php` a doplníme metody.

- `up()` je použita defaultně, při migraci na novější verzi - obvykle **přidáváme data**
- `down()` pak migruje zpět na starší verzi - obvykle **mažeme data** 

Více bude jasnější ze samotného SQL zápisu:

```language-php
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
```

Tím naše práce s SQL končí.

Když znovu dáme status...

```
$ php www/index.php migrations:status
```

...vidíme, že máme jednu novou migraci (dosud neaplikovanou na databázi).

```language-bash
# ...

    >> Executed Migrations:                                0
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               1
    >> New Migrations:                                     1
```

<a name="apply-new-migrations"></a>


### 4. Teď si konečně sáhneme na databázi

Aplikujeme všechny nové změny:

```language-bash
php www/index.php migrations:migrate
```

```language-bash
Loading configuration from the integration code of your framework (setter).

                    Doctrine Database Migrations


WARNING! You are about to execute a database migration that could result in schema changes and data lost.
Are you sure you wish to continue? (y/n)
```

Potvrdíme, že cheme opravdu migrovat: "y"


```language-bash
Migrating up to 20151031185555 from 0

  ++ migrating 20151031185555

     -> CREATE TABLE "article" ("id" serial NOT NULL, "name" text NOT NULL);

  ++ migrated (0.02s)

  ------------------------

  ++ finished in 0.02
  ++ 1 migrations executed
  ++ 1 sql queries
```

A vidíme, že tabulka `article` byla úspěšně vytvořena.

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015/09/15/7-success-meme.jpg" alt="You own this!">
</div>
