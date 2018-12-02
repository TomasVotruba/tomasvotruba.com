---
id: 97
title: To nejlepší ze SymfonyConu 2015
perex: "Každoročně pořádaný SymfonyCon se letos konal v Paříži. Přes všechny útoky Paříž žije dál. Možná o to víc. Důkazem toho může být i 1000 návštěvníků, kteří na akci dorazili. Čechy jsem reprezentoval spolu s Dennisem, a taky Petrem a Kubou z Lmc.eu"
related_items: [2]

deprecated_since: "August 2017"
deprecated_message: "This post is available only in Czech and whole website was moved to English."

lang: cs
---

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-divadlo.jpg" alt="Šatny, jídelna a coffee tables">
    <br>
    <em>Fronta na Symfony slony. Za 2 hodiny už žádný nezbyl.</em>
</div>
<br>

A teď k dění. V krásném divadle a přidruženém kinosále se odehrálo přes 20 přednášek.

Dnes se spolu podíváme na 4 nejlepší.


## 1. 10 years of Symfony

Symfony už je tu s námi 10 let. Projekt sice založil Fabien Potencier, ale stojí za ním daleko víc osobností, které jej posouvají směrem k větší srozumitelnosti a použitelnosti.

Fabien zmínil přes 20 lidí, které na své cestě postupně potkal, a ukázal, kdo s čím do Symfony přispěl. V jeho podání to znělo jako hračka. Velmi inspirující pro ty, kteří chtějí škálovat své open-source projekty a dopřát jim dlouhý život.

### 1 věc, kterou stojí za to si zapamatovat

Mezi lidmi představil i [nový release process](https://symfony.com/blog/improving-the-symfony-release-process), který usnadní <em>continuous upgrade</em>.

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-release-process.jpg" alt="Nový release process">
</div>
<br>

Jednoduše řečeno:

- s každou novou major verzí vyjde i stará
- obě budou LTS
- obě budou mít stejné featury
- stejně jako 2.8 a 3.0

Přechod na nové major verze bude pod mnohem větší kontrolou než kdy dřív.


## 2. Symfony2 at BlaBlaCar

Tato zdánlivě random-generated firma se zabývá bla bla bla… ridesharingem, tedy spolujízdou.

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-bla-bla-car.png" alt="Ušetři za spolujízdu">
</div>
<br>

Šlo o case study firmy, která začínala jako malý startup, rychle rostla a musela se naučit škálovat za pochodu. Vyzkoušeli spoustu cest, které nefungovaly, aby našli ty správné (pro jejich kontext samozřejmě).

Právě o tyto “do’s and don't’s” se s námi přednášející podělili:

- upgrade as soon as you can
- failure is beginning
- Doctrine - fast, but issues with MVC, cache, integrity problems, scale, decoupling...
- Event Dispatching v RabbitMQ

### 1 věc, kterou stojí za to si zapamatovat

Čím se lišil jejich vývoj od jiných aplikací, kde se obvykle přechází monolitického repositáře k odděleným microservices za použítí REST API?

Jejich microservices nevyužívají interní API, ktére jim přišlo zbytečně náročné na udržování, ale vlastní řešení.

Říkají mu **The Gateway** ([slajd](https://speakerdeck.com/odolbeau/symfony-at-blablacar?slide=64)) a jeho předností jsou DDD, oddělení business logiky a přístwupu k datům a přehledné organizaci. Zajímavá myšlenka.

> Máš taky startup a řešíš něco podobného? Mrkni [na slajdy](https://speakerdeck.com/odolbeau/symfony-at-blablacar).


## 3. New Symfony Tips and Tricks

A teď něco pro každého:

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-tips-and-tricks.jpg" alt="Tipy a triky">
</div>
<br>

[Javier Eguiluz](https://twitter.com/javiereguiluz), kterého všichni známe jako:

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-javier.png" alt="Seflie?">
</div>
<br>

se s námi podělil o tipy a triky, které posbíral za poslední rok - nejen při psaní [Week of Symfony](https://symfony.com/blog/category/a-week-of-symfony) (doporučuju odebírat, pokud chceš být v obraze).

Za mě to byla ta nejzajímavější přednáška, ze které jsem si odnesl velké množství tipů do vlastní praxe.

**Tipy jsou pro začátečníky i pokročilé**, jako třeba

- nested Doctrine transactions
- custom logger formatter - už žádné ošklivé nepřehledné logy

Přednáška byla nabitá užitečnými tipy, které se jednoduše nedaly pobrat všechny naráz.

> Naštěstí [jsou ve slajdech](https://www.slideshare.net/javier.eguiluz/new-symfony-tips-tricks-symfonycon-paris-2015), kam si pro ně můžeš sáhnout.

Potěšilo mě, že pár tipů bylo i od [Martina Hasoně](https://twitter.com/hasonm).

Tento skvělý přehled mě inspiroval mrknout i [na verzi z minulého roku](https://www.slideshare.net/javier.eguiluz/symfony-tips-and-tricks).


### 1 věc, kterou stojí za to si zapamatovat

Pokud přebíráš aplikaci bez testů a chceš přidat aspoň nějakou kontrolu, bude se ti hodit "smoke testing" pro všechny služby:

```php
public function testContainerServices()
{
	$client = static::createClient();

	foreach ($client->getContainer()->getServiceIds() as $serviceId) {
        $service = $client->getContainer()->get($serviceId);
        $this->assertNotNull($service);
	}
}
```


## 4. Symfony: Your next Microframework

Ryan Weavera žeru. Určitě ho znáš i ty - minimálně ze Symfony blogu, na který dává velmi záživné čtení. A stejně záživný a vtipný (možná ještě vtipnější) je i osobně. Jeho přednáška o tom, že Symfony lze nově použít i jako microframework (díky [MicroKernelTrait](https://github.com/symfony/symfony/blob/3.0/src/Symfony/Bundle/FrameworkBundle/Kernel/MicroKernelTrait.php)), byla naprosto skvělá.

Ukázal nám nové možnosti a způsoby...

- jak mít “více aplikací” v jednom projektu,
- jak udělat minimalistický web se všemi magickými vychytávkami Symfony,
- a hlavně, jak zabít Silex, protože touto přednáškou ho totálně oddělal.

Na toto téma už brzy na Zdrojáku vyjde článek, kde si vše podrobně ukážeme.

> Zatím můžeš mrknout [na slajdy z přednášky](https://www.slideshare.net/weaverryan/symfony-your-next-microframework-symfonycon-2015).

### 1 věc, kterou stojí za to si zapamatovat

Dříve byla volba jasná:

- Silex pro menší a jednoduché aplikace
- Symfony pro ty větší

Kdy teda použít MicroKernel?

- MicroKernel se hodí, když začínáš malou aplikaci, ale chceš ji časem škálovat. Navíc oproti Silexu podporuje Bundly.


### Další přednášky, které stojí minimálně za proklikání:

- Marc Morena - When e-commerce meets Symfony ([mrkni na slajdy](https://www.slideshare.net/MarcMorera/when-ecommercemeetssymfonyparissymfonycon2015))
- Benjamin Eberlei - Doctrine 2 - to use or not to use ([mrkni na slajdy](https://qafoo.com/resources/presentations/symfonycon_paris_2015/doctrine2_to_use_or_not_to_use.html))

**Všechny dostupné slajdy z konference najdeš na [Joind.in](https://joind.in/event/symfonycon-paris-2015/schedule/list).**

Až budou k dispozici záznamy z jednotlivých přednášek, dáme vědět na Twitteru. Tak sleduj [@Pehapkari](https://twitter.com/pehapkari).


## Co jsem si teda z konference odnesl?

Kromě 5 slonů, 1 trička a kontaktů na lead vývojáře open-sources projektů, jsem se na konferenci seznámil...

- s nejnovějšími trendy ve vývoji Symfony a jiným velkých projektů
- s tím, jak budovat komunitu postavenou rovnosti a na lidech
- s lidmi s z open-source prostředí, které jsem dříve znal jen z avatara na Githubu

A spoustu chuťových zážitků bizardní francouzské kuchyně :).


<br>

<div class="text-center">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-stage.jpg" alt="Co uděláš wifi v divadle?">
</div>

<br>


### Pojeď za rok taky, vždyť je to kousek

<div class="pull-left" style="margin-right:1.2em">
    <img src="/assets/images/posts/2015/symfonycon/symfonycon-to-berlin.jpg" alt="Do Berlína!">
</div>

Příští Symfonycon bude v Berlíně, což je vlakem z Prahy jen 4,5 hoďky. Registrace sice zatím otevřena není, ale stačí sledovat [@Symfonycon](https://twitter.com/SymfonyCon) a budeš o tom vědet mezi prvními.

Vyplatí se to! Tento rok byly early birds lístky za 209 €.

<p style="font-size:0.9em">
    <em>S psaním článku mi pomohl <a href="http://defr.cz/">Dennis</a>.</em>
</p>

<div class="clearfix"></div>
