---
id: 2
title: 4 žhavé novinky v Symfony 3
categories:
    - Symfony
perex: "V listopadu 2015 má kromě PHP 7 a Drupalu 8, vyjít i Symfony 3. Jaké přináší změny a novinky?"
---

<p class="perex" markdown="1">
    V listopadu 2015 má kromě [PHP 7](https://wiki.php.net/rfc/php7timeline) a [Drupalu 8](https://www.drupal.org/node/2605142), vyjít i Symfony 3. Jaké přináší změny a novinky?
</p>

Symfony už toho umí opravdu hodně. Nová verze klade velký důraz především na [DX (developer experience)](http://symfony.com/blog/making-the-symfony-experience-exceptional). Přináší nám **jednodušší a jednoznačné API**, **lepší decoupling komponent**, **integraci standardů [PSR-3](http://www.php-fig.org/psr/psr-3/) a [PSR-7](http://symfony.com/doc/current/cookbook/psr7.html)**. A spoustu dalších novinek, díky kterým bude psaní aplikací zase o něco zábavnější.


### V tomto článku se dozvíš

- kdy vyjde která verze a jaký bude jejich praktický dopad
- jaké jsou 4 nejzajímavější novinky


## Kdy vyjde která verze?

Zažil jsi migraci ze Symfony 1 na 2 a chceš se vyhnout podobnému masakru? Neměj obavy - novinek je sice spousta, ale Symfony se poučilo a nedá dopustit na [zpětnou kompatibilitu](http://symfony.com/doc/current/contributing/code/bc.html).

Migrace Symfony 2 na 3 bude značně zjednodušena tím, že **spolu s verzí 3 vyjde i verze 2.8**. **Ta bude mít všechny nové featury verze 3 a bude obsahovat BC vrstvu k sérii 2.x**. Verze 2.8 bude long term support (LTS) - můžeš tak počítat **s podporou až do konce roku 2018**. 

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/release-plan.png" alt="Release plan">
    <br>
    <em>Verze 2.8 bude LTS. První LTS nové série bude až 3.3 (vyjde v květnu 2017).</em>
</div>

<br>

Jaké jsou tedy 2 hlavní rozdíly mezi 3.0 a 2.8?

- min. verze PHP 5.5
- odstraněný veškerý deprecated kód, který poskytuje BC kompatibilitu k 2.x (~ 10 % kódu)

## A nyní ke 4 očekávaným novinkám

### 1. Autowiring služeb

Symfony nyní podporuje constructor autowiring. Při vytváření definice služby tak můžete zapnout `autowiring` a přeskočit manuální předávání argumentů.
V českém prostředí je autowiring poměrně populární díky bundlům jako [Kutny](https://github.com/kutny/autowiring-bundle), [Skrz](https://github.com/skrz/autowiring-bundle) a [Symnedi](https://github.com/Symnedi/AutowiringBundle). 

#### Jak to v praxi vypadá?

Dřívější dlouhý zápis

```language-yaml
# services.yml 

services:
	myService:
		class: MyBundle\MyService
    	arguments: [ @dependency1, @dependency2 ]
	
	dependency1:
		class: MyBundle\Dependency1
	
	dependency2:
		class: MyBundle\Dependency2
```

nyní můžeš zkrátit na

```language-yaml
# services.yml

services:
	myService:
		class: MyBundle\MyService
	    autowiring: true
```

#### Jak to funguje?

Dependency Injection container zanalyzuje konstruktor služby a:

- pokud jsou služby dostupné → předá je
- pokud ne → registruje je jako privátní služby

#### Jak je to s interface?

Místo konkretního typu služby můžeš vyžádat interface, který služba implementuje. Ale co když máme více služeb jednoho interface (typické pro chain pattern)? Stačí u dané služby explicitně uvést:

```language-yaml
# services.yml

services:   
    dependency1:
	    class: MyBundle\Dependency1
	    autowiring_types: MyBundle\MyInterface
```

<blockquote>
    Chceš vědět víc? Mrkni na
    <a href="https://github.com/symfony/symfony/pull/15613">
        <em class="fa fa-github"></em>
        pull-request
    </a>
</blockquote>

<hr>

### 2. Logičtější složky

Symfony 3 full-stack s sebou přináší řád. Zbaví nás chaosu ve složce `/app`.

#### Jak?

Dočasné soubory, logy, nastavení pro PHPUnit, konzolové soubory...
<br>To vše má nyní jasné umístění oddělené od kódu naší aplikace. 

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/directory-structure.png" alt="Release plan">
    <br>
    <em>
        Console skripty najdeš nyní ve složce <code>/bin</code>.
        Dočasné soubory <code>/cache</code> a <code>/logs</code> pak ve složce <code>/var</code>.
    </em>
</div>

<br>

Testy pak v příkazové řádce spustíš jednoduše přes <code>phpunit</code>.

<hr>

### 3. Symfony profiler v novém kabátku

Pro programátora je důležitý nejen přehledný kód, ale i přehledné meta-informace o aplikaci. Ty si v Symfony snadno zobrazí 
pomocí Symfony Profileru.
 
Ten už zobrazoval tolik informací, že se v něm programátor začal ztrácet. Po 4 letech se konečně dočkal flat designu.
 
Důležité informace a hlavně chybové hlášky jsou teď mnohem čitelnější.

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/profiler-before-after.png" alt="Nový design" style="max-width:60%">
    <br>
    <em>Srovnání staré a nové verze</em>
</div>

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/profiler-old-new.png" alt="Starý a nový design">
    <br>
    <em>
        Dříve bylo těžké se rychle zorientovat ve velkém množství informací.
        <br>Teď ty důležité najdeš hned na začátku stránky
    </em>
</div>

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/profiler-go-back.gif" alt="Odkaz na stránku" style="max-width:60%">
    <br>
    <em>Snadno se dostaneš z profileru zpět na stránku</em>
</div>

<br>

<blockquote>
    Zajímá tě víc? Mrkni na
    <a href="https://github.com/symfony/symfony/pull/15523">
        <em class="fa fa-github"></em>
        pull-request
    </a>
</blockquote>

<hr>

### 4. Micro Kernel

Velkou radost budou mít vývojáři menších aplikací, kteří si rádi užívají komfort ekosystému full-stack Symfony. Před pár dny, 5. listopadu, byl do FrameworkBundle přidán **Micro Kernel**.

Ten je vhodný právě na aplikace, které vyžadují jednoduchou konfiguraci, bundly a na které Silex nestačí.

kMicro Kernel konkrétně:

- nevyžaduje žádné další konfigurační soubory
- umožňuje přidání extension bez bundlů 
- podporuje routování 

#### Jak takový Micro Kernel vypadá?

```language-php
<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class ConcreteMicroKernel extends Kernel
{
    use MicroKernelTrait;

    public function halloweenAction()
    {
        return new Response('halloween');
    }

    public function registerBundles()
    {
        return [new FrameworkBundle()];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/', 'kernel:halloweenAction');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => '$ecret',
        ]);

        $containerBuilder->setParameter('title', 'Symfony 3 is painless');
    }
}
```

<blockquote>
    Zajímá tě víc? Mrkni na
    <a href="https://github.com/symfony/symfony/pull/15990">
        <em class="fa fa-github"></em>
        pull-request
    </a>
</blockquote>

<hr>

## Teď už víš...

- Že verze 2.8 bude LTS a vyjde spolu s verzí 3.0.
- Jak ti autowiring ušetří práci při psaní definicí služeb.
- Jak si uklidit složku `/app`, aby to dávalo smysl.
- Že práce s profilerem bude daleko přehlednější.
- A že pro malé aplikace máš k dispozici Mikro Kernel.

## V Symfony zase vědí...

- Že když programátor může sáhnout po jednoduším řešení, udělá to.

Proto se snaží, aby jeho použití bylo bez překážek.

Už máš chuť si to vyzkoušet? Hned v příštím článku si ukážeme, *jak to rozjet v Symfony 3*.

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/you-got-this-meme.png" alt="A máš to">
</div>

<br>
