---
id: 86
title: "New in Easy Coding Standard 4: Clean Symfony Standard with Yaml and Services"
perex: |
    I wrote about [news in Easy Coding Standard 3](/blog/2018/03/01/new-in-symplify-3-4-improvements-in-easy-coding-standard/) a while ago. EasyCodingStandard 4 is released yet (still in alpha), but soon you'll be able to use all the news I'll show you today.
    <br><br>
    And what are they? Neon to YAML, semi-static to Services, customizable caching, even simpler skipper, short bin and more.

tweet: "New Post on my Blog: New in Easy Coding Standard 4: Clean Symfony Standard with Yaml and Services"
tweet_image: "/assets/images/posts/2018/symplify-4-ecs/yaml-autocomplete.gif"

updated_since: "August 2020"
updated_message: |
    Updated ECS YAML to PHP configuration since **ECS 8**.
---

## 1. Configure Caching Directory

<a href="https://github.com/symplify/symplify/pull/656" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #661
</a>

Docker users will be happy for this feature, since it makes ECS much more usable. To enjoy speed of caching of changed files on second run, just tune your config.

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('cache_directory', '.ecs_cache');
};
```

Thank you [Marcin Michalski](https://github.com/marmichalski) for adding this feature.

<br>

## 2. Skip Anything, Anywhere

<a href="https://github.com/symplify/symplify/pull/661" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #661
</a>

One of the features I really like is skipping particular spots. PHP CS Fixer and PHP_CodeSniffer can ignore whole directory, 1 sniff everywhere or force to put annotation to your code and that's not the way to go. **Your code should have no idea about tools you use to analyze it**.

What you really need? Exclude 1 file but only for 1 checker. Or 1 checker for group of files and sometimes only 1 code from sniff on 1 file. That all is possible now.

**Because details matters and it's pointless to think about code or class**, you can now remove `skip_codes` key from your config and use `skip` section only.

<br>

## 3. Short `vendor/bin/ecs` is the King

<a href="https://github.com/symplify/symplify/pull/647" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #647
</a>

One last detail. Did you use this bin file to run ECS?

```bash
vendor/bin/easy-coding-standard
# or
vendor/bin/easy-coding-standard.php
```

I know it's pain, mainly during live demo presentations with all that tyops :).

Now this is the only way to use ECS:

```bash
vendor/bin/ecs
```

Typo proof or at least less error prone. Just change it in you [`composer.json`'s `script` section](https://blog.martinhujer.cz/have-you-tried-composer-scripts/) or CI setups and you're ready to go!

<br>

## 4. DI Migration Finished: From Neon to YAML

<a href="https://github.com/symplify/symplify/pull/651" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #651
</a>

Symplify used `Nette\DI` a long time ago and with it its markup language - Neon. Then it moved to `Symfony\DependencyInjection` in [Symplify 2.0](https://github.com/symplify/symplify/blob/master/CHANGELOG.md#v200---2017-06-16), because it was just impossible to reject [all these awesome Symfony 3.3 features](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/) by Nicolas Grekas. But this was just partial migration - Neon files still worked.

That lead to situation, where 5 *custom-cool-classes* simulated loading transforming Neon to YAML format, merging it and then passing to Symfony Container, hoping all went well. And it worked. Well, most of the times.

Based on feedback from [community around Symplify](https://github.com/symplify/symplify/issues/565), rejection of ECS in [Doctrine\CodingStandard](https://github.com/doctrine/coding-standard) where Neon was one of reasons and weird feeling from promoting "local-only standard", I decided to move to Symfony completely.

<a href="https://xkcd.com/927/">
    <img src="https://imgs.xkcd.com/comics/standards.png">
</a>

I had one problem - missed services autocomplete in Yaml files. But you know what they say:

<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    There are no solutions. There are only trade-offs
</blockquote>

I hear you community, so lets trade! **From ECS 4, you can use Yaml everywhere with syntax you know, behavior from Symfony ecosystem you know and with no need to learn new standard.**

### How to Migrate?

Well just rename `easy-coding-standard.neon` or `ecs.yml` and
 then read about it in [Neon vs. Yaml and How to Migrate Between Them](/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/).

<br>

## 5. From Semi-Static Checkers to Services as First-Class Citizen

**Note: Symplify 8 now uses PHP configuration.**

<a href="https://github.com/symplify/symplify/pull/660" class="btn btn-dark btn-sm mt-2 mb-3">
    <em class="fab fa-github"></em>
    &nbsp;
    Check the PR #660
</a>

Thanks to Yaml, we could use finally use full power of Symfony\DependencyInjection component, constructor injection, autowiring... again, all that you probably already know from Symfony.

Why? **ECS is basically a Symfony application with DI Container**. It loads all checkers from config you provide, turns them into services and then uses those services to check the code.

YAML was the only missing part to do this. And ECS has it now, so does the explicit services!
And you can do and use any feature you Symfony know. Magic no more #metoo.

<br><br>

Happy upgrading!
