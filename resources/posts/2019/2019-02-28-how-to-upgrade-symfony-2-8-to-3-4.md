---
id: 190
title: "How to Upgrade Symfony&nbsp;2.8&nbsp;to&nbsp;3.4"
perex: |
    Are you Symfony programmer? Do you work on a successful project? Then upgrading the Symfony project is a work you can't avoid.
    Almost a year ago I wrote about [Five and Half Steps to Migrate from Symfony 2.8 LTS to Symfony 3.4 LTS in Real PRs](https://blog.shopsys.com/5-5-steps-to-migrate-from-symfony-2-8-lts-to-symfony-3-4-lts-in-real-prs-50c98eb0e9f6).


    Now it's much easier to jump from one LTS to another - with **instant upgrades**.

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

Recently, more and more issues pop-up at Symfony repository **asking for automated upgrade**:

- [Why not to create a symfony framework updater software from command line](https://github.com/symfony/symfony/issues/30054)

I'm happy Symfony core team is supporting generic solution to all PHP-code upgrades ↓

<img src="/assets/images/posts/2019/symfony-up/nick.png" class="img-thumbnail">

## Upgrade Symfony... and PHP

To make a bad situation more complicated, this upgrade is also related to upgrading of PHP - **Symfony 3.4 requires PHP 5.5.**

I wrote about [PHP upgrades before](/blog/2018/11/08/fatal-error-uncaught-error-operator-not-supported-for-strings-in/), but the main away is to upgrade **one minor version at once**:

- Symfony 2.8 → 3.0
- PHP 5.3 → 5.4
- Symfony 3.0 → 3.1
- Symfony 3.1 → 3.2
- PHP 5.4 → 5.5
- Symfony 3.2 → 3.3
- Symfony 3.3 → 3.4

If you split each of these lines into standalone pull-requests, you're the best!


## Forget `UPGRADE.md`

You probably know I work almost part-time on [the Rector project](https://getrector.com). I gather feedback from conferences and meetups all over Europe and try to make Rector better every day. Recently he also migrated between [2 PHP frameworks](/blog/2019/02/21/how-we-migrated-from-nette-to-symfony-in-3-weeks-part-1), because why not?

The PHP community gives me positive vibes about going the right direction. It helps me to make PHP and Symfony **sets more and more complete**.

## How to Upgrade then?

All you need to do to upgrade your PHP code is to install Rector and run particular upgrades.

**Do you want to upgrade from Symfony 2.8 to 3.4?**

```bash
composer require rector/rector --dev
```

Create `rector.php` config:

```bash
vendor/bin/rector init
```

Add Symfony sets in it:

```php
use Rector\Symfony\Set\SymfonySetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SymfonySetList::SYMFONY_28);
    // take it 1 set at a time to so next set works with output of the previous set; I do 1 set per pull-request
    // $rectorConfig->import(SymfonySetList::SYMFONY_30);
    // $rectorConfig->import(SymfonySetList::SYMFONY_31);
    // $rectorConfig->import(SymfonySetList::SYMFONY_32);
    // $rectorConfig->import(SymfonySetList::SYMFONY_33);
    // $rectorConfig->import(SymfonySetList::SYMFONY_34);

    // set paths to directories with your code
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
};
```

**Are you stuck on old PHP 5.3?** Rector got you covered:

```php
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SetList::PHP_53);
    // again 1 set at a time
    // $rectorConfig->import(SetList::PHP_54);
    // $rectorConfig->import(SetList::PHP_55);
    // $rectorConfig->import(SetList::PHP_56);
    // ...
};
```

## Awesome Symfony 3.3+ Dependency Injection

Upgrade to Symfony 3.3 shrunk my configs to 1/5 of its original size. That's the  #1 reason you want to upgrade. If you don't know what I'm talking about, check [the diff post about those features](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/). But wait, don't do it manually! **[This tool converts it all](/blog/2018/12/27/how-to-convert-all-your-symfony-service-configs-to-autodiscovery/) for you.**

<br>

And that's how we upgrade in 2019 :)

<br>

Happy coding!
