---
title: Jak přežít Symfony 3
categories:
    - Symfony
---

V listopadu 2015 má kromě PHP 7 a Drupalu 8, vyjít i Symfony 3. Jaké přináší změny a novinky? A jak si jej snadno vyzkoušet?

Symfony už toho umí hodně. Nová verze klade velký důraz na DX (developer experience). Přináší nám jednodušší a jednoznačné API, lepší decoupling komponent, integraci PSR-3. A spoustu dalších novinek, díky kterým bude psaní aplikací zase o něco zábavnější.


## Na co se tedy podíváme

- na přehled důležitých verzí a jejich praktický význam
- na 5 nejzajímavějších novinek


## Kdy vyjde jaká verze

Zažil jsi migraci ze Symfony 1 na 2 a chceš se vyhnout podobnému masakru? Neměj obavy - novinek je sice spousta, ale Symfony se poučilo a nedá dopustit na zpětnou kompatibilitu.

Upgrade značně zjednodušuje tím, že **spolu s verzí 3 vyjde i verze 2.8**. Ta bude mít všechny nové featury verze 3, a bude obsahovat BC vrstvu k sérii 2.x.

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/release-plan.png" alt="Release plan">
    <br>
    <em>Verze 2.8 bude LTS, zatímco první LTS nové série bude 3.3 (vyjde v květnu 2017).</em>
</div>

todo: pořešit zobrazování i na mobilu. vyzkoušet, some width-scale-tag n stuffs...

<br>

Jaké jsou tedy 2 hlavní rozdíly mezi 3.0 a 2.8?

- min. verze PHP 5.5
- odstraněný veškerý deprecated kód, který poskytuje BC kompatibilitu k 2.x

## Novinky, které ti zpříjemní práci

### Autowiring služeb

V českém prostředí je autowiring poměrně populární díky bundlům jako [Kutny](https://github.com/kutny/autowiring-bundle), [Skrz](https://github.com/skrz/autowiring-bundle) a [Symnedi](https://github.com/Symnedi/AutowiringBundle). 
Symfony nyní podporuje constructor autowiring.

Dřívější dlouhý zápis

```language-yaml
// services.yml 

services:
	myService:
		class: MyBundle\MyService
	arguments: [ dependency1, dependency2 ]
	dependency1:
		class: MyBundle\Dependency1
	dependency2:
		class: MyBundle\Dependency2
```

nyní můžeš zkrátit na

```language-yaml
// services.yml

services:
	myService:
		class: MyBundle\MyService
	autowiring: true
```

Dependency Injection container zanalyzuje konstruktor služby a:

- pokud jsou služby dostupné, předá je
- pokud ne, registruje je jako privátní služby


Autowiring sice podporuje i interface. Ale co když máme více služeb jednoho interface (typické pro chain pattern)? Stačí u dané služby explicitně uvést:

```language-yaml
dependency1:
	class: MyBundle\Dependency1
	autowiring_types: MyBundle\MyInterface
```

Zajímá tě víc? Mrkni na [pull-request](https://github.com/symfony/symfony/pull/15613).


### Logičtější složky

Běžným konvencím se dostalo i složkám.

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/directory-structure.png" alt="Release plan">
    <br>
    <em>Console skripty najdeš nyní ve složce `/bin`. Dočasné soubory `/cache` a `/logs` pak ve složce `/var`.</em>
</div>

<br>

Spouštění testů je prosté

```language-bash
$ phpunit
```


### Symfony profiler v novém kabátku

Flat designu se dočkal profiler i debug bar. Důležité informace a hlavně chybové hlášky jsou teď mnohem čitelnější.

Todo: obrázky nového a starého pod sebe

Todo: obrázky nového a starého panelu pod sebe


Více viz pull-request


Todo: Hodí se? Chci aby šlo škálovat!

### Stále málo

Nestačí ti jich pár, ale chceš jít do hloubky? Můžeš pokračovat na dalších zdrojích:

- polyfil
- micro-kernel
- guard component

- some články




## Vyzkoušej si Symfony 3 ve třech krocích

Pokud tě stejně jako mě zajímá, jak to v Symfony 3 vlastně vypadá, vyzkoušejte si full-stack sandbox.

Jak na to? (todo: try na windwos!) 

1) Nainstaluj si jej přes composer

```language-bash
$ SENSIOLABS_ENABLE_NEW_DIRECTORY_STRUCTURE=true composer create-project symfony/framework-standard-edition myproject @dev
```

2) Spusť lokální php server

```language-bash
$ cd myproject
$ php bin/console server:run localhost:8001
```

3) Otevři v prohlížeči

[http://localhost:8001](http://localhost:8001)

<br>

<div class="text-center">
    <img src="/../../../../images/posts/2015-11-08/welcome.png" alt="Welcome screen">
    <br>
    <em>Co na něj říkáte?</em>
</div>

<br> 
 

## V Symfony 3 nás čeká

- přehlednější struktura složek
- podpora autowiringu
- jednotné api služeb
- hezčí a přehlednější profiler

V Symfony vědí, že když programátor může sáhnout po jednoduším řešení, udělá to.
<br>
Proto se snaží, aby jeho použití bylo bez překážek.
