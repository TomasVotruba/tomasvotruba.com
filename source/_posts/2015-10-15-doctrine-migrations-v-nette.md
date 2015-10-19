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
    # nesmí chybět Kdyby\Doctrine nebo jiná integrace Doctrine
```


## Zkouška spojení

Že vše proběhlo v pořádku, prověříme spuštěním z přikažového řádku. V Nette to znamená využít `Kdyby\Console`.

Zkusíme tedy vypsat všechny příkazy týkající se migrací.
  
```bash
php www/index.php list migrations
```

Pokud vidíme přehled příkazů, máme vyhráno:

![Příkazy pro migrace](../../../../images/posts/2015/09/15/1-list-migrations.png)

A můžeme používat!

---

## Profi workflow ve 4 krocích

V [Lékárně.cz](http://lekarna.cz/) používáme jednoduchý proces, který pokrývá 90 % případů. Tady je.

*Od šéfa jsme dostali zadání: vytvořit tabulku na články.* To je výzva, tak jdem na to!

### 1. Zkontrolujeme status

```sh
php www/index.php migrations:status
```

![Jaký máme status](../../../../images/posts/2015/09/15/2-migrations-status.png)

Důležitá jsou poslední 2 čísla. Vypadá to, že vše je aktuální, tak můžeme pokračovat.

### 2. Vytvoříme si novou prázdnou migraci

```sh
php www/index.php migrations:generate
```

![Nová migrace](../../../../images/posts/2015/09/15/3-generate.png)

Název migrace je generován automaticky dle timestampu. Tady je použita defaultní cesta, složka `/migrations`. Mrknem tam!


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
class Version20151019192347 extends AbstractMigration
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

![Nová migrace](../../../../images/posts/2015/09/15/4-status-with-new-migration.png)

Ukáže se nám mezi "New migrations" právě díky tomu, že nebyla aplikována na databázi.

### 4. Teď si konečně sáhneme na databázi!

Aplikujeme všechny nové změny:

```sh
php www/index.php migrations:migrate
```

![Nová migrace](../../../../images/posts/2015/09/15/5-migrate-success.png)


A je to! Gratuluju, jste připraveni migrovat!

![Skvělá práce!](../../../../images/posts/2015/09/15/7-success-meme.jpg)

---

### Tip na závěr: Manuální migrace jedné migrace

Pokud migrujeme více zásahů a cheme mít celý proces pod kontrolou, můžeme migrovat i po jedné:

```sh
php www/index.php migrations:execute 20151019192347
```

![Po jedné](../../../../images/posts/2015/09/15/6-migrate-single.png)

Pro vrácení zpět, stačí přidat `--down`.

```sh
php www/index.php migrations:execute 20151019192347 --down
```
