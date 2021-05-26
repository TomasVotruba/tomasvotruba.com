---
id: 262
title: "How we Upgraded Pehapkari.cz from Symfony 4 to 5 in 25 days"
perex: |
    A month ago, Symfony 5 has been released. Upgrading of such a small web as our community website **must be easy**, right?
    <br>
    <br>
    Well, that's what we thought. **Were we right or wrong?**

tweet: "New Post my Blog: How we Upgraded Pehapkari.cz from #symfony 4 to 5 in 25 days"
tweet_image: "/assets/images/posts/2019/symfony5_pr.png"

updated_since: "November 2020"
updated_message: |
    Switch from deprecated `--set` option to `rector.php` config.
---

This post is based on the real problems we faced when we upgraded our website. **It is full of experience** with pieces of explanation, real code snippets in diffs, painful frustration of Symfony ecosystem and bright light at the end of the tunnel.

<br>

Are you ready? Let's dive in ↓

<br>

## 1. Easy picks

### Twig 2 → 3

Before, you could connect `for` with `if` like this:

```twig
{% for post in posts if post.isPublic() %}
    {{ post.title }}
{% endfor %}
```

Now [the `filter`](https://twig.symfony.com/doc/3.x/filters/filter.html) has to be used:

```twig
{% for post in posts|filter(post => post.isPublic()) %}
    {{ post.title }}
{% endfor %}
```

*Thanks [Patrik for the tip](https://www.reddit.com/r/PHP/comments/ef2nit/how_we_upgraded_pehapkaricz_from_symfony_4_to_5/fbzyhsl)*

## 2. Rector Helps You with PHP

Do you want to know, what is needed for the upgrade to Symfony 5? [Just read upgrade notes](https://github.com/symfony/symfony/blob/5.0/UPGRADE-5.0.md) in Symfony repository.

1. For PHP stuff, use [Rector](https://github.com/rectorphp/rector):

```bash
# install Rector
composer require rector/rector --dev

# or in case of conflicts
composer require rector/rector-prefixed --dev
```

Rector has minimal sets, meaning each minor version is standalone and independent.
What does that mean? For upgrading from Symfony 4 to 5, you need to **run all the minor version sets**, one by one.

2. Update `rector.php` config

```php
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::SYMFONY_41,
        // take it 1 set at a time to so next set works with output of the previous set; I do 1 set per pull-request
        // SetList::SYMFONY_42,
        // SetList::SYMFONY_43,
        // SetList::SYMFONY_44,
        // SetList::SYMFONY_50,
    ]);
};
```

3. Run Rector

```bash
vendor/bin/rector process app src tests
```

Verify, check that CI passes and then continue with next Symfony minor version.

## 3. Update `composer.json` before `composer update`

### Easy Admin Bundle

```diff
 {
     "require": {
-        "alterphp/easyadmin-extension-bundle": "^2.1",
+        "alterphp/easyadmin-extension-bundle": "^3.0",
    }
}
```

### Doctrine

```diff
 {
     "require": {
-        "doctrine/cache": "^1.8",
+        "doctrine/cache": "^1.10",
-        "doctrine/doctrine-bundle": "^1.11",
+        "doctrine/doctrine-bundle": "^2.0",
-        "doctrine/orm": "^2.6",
+        "doctrine/orm": "^2.7",
     }
 }
```

### Doctrine Behaviors

```diff
 {
     "require": {
-        "stof/doctrine-extensions-bundle": "^1.3",
-        "knplabs/doctrine-behaviors": "^1.6"
+        "knplabs/doctrine-behaviors": "^2.0"
     }
 }
```

### Sentry

```diff
 {
     "require": {
-        "sentry/sentry-symfony": "^3.2",
+        "sentry/sentry-symfony": "^3.4",
     }
 }
```

### Twig Extensions were Removed

```diff
 {
     "require": {
-        "twig/extensions": "^1.5"
     }
 }
```

This might be scary at first, depends on how many of those functions have you used.

Look at the [README on Github](https://github.com/twigphp/Twig-extensions) to find out more:

<div class="text-center">
    <img src="/assets/images/posts/2019/symfony5_twig_extension_readme.png" class="img-thumbnail">
</div>

## 4. Symfony Packages in `composer.json`

Do you use Flex and config `*` version?

```json
{
    "require": {
        "symfony/console": "*",
        "symfony/event-disptacher": "*"
    },
    "extra": {
        "symfony": {
            "require": "^4.4"
        }
    }
}
```

Not sure why, but in some cases, it failed and **blocked from the upgrading**. **I had to switch to explicit version per package, to resolve it:**

```diff
 {
     "require": {
-        "symfony/console": "*",
+        "symfony/console": "^4.4",
-        "symfony/event-disptacher": "*"
+        "symfony/event-disptacher": "^4.4"
-    },
+    }
-    "extra": {
-        "symfony": {
-            "require": "^4.4"
-        }
-    }
 }
``````

Then switch to Symfony 5:

```diff
-"symfony/asset": "^4.4",
+"symfony/asset": "^5.0",
-"symfony/console": "^4.4",
+"symfony/console": "^5.0",

// etc.
```

But some packages are released out of [monorepo cycle](/blog/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask/):

```diff
-"symfony/maker-bundle": "^1.14",
+"symfony/maker-bundle": "^1.13",
```

All right, now you run...

```bash
composer update
```

...and get new packages with Symfony 5... or probably a lot of composer version conflicts.

### Symfony Packages WTFs in

In Symfony 5, some packages were **removed**:

```diff
-"symfony/error-renderer": "^4.4",
```

```diff
-"symfony/web-server-bundle": "^4.4",
```

<br>

Some packages were **replaced by new ones**:

```diff
-"symfony/error-debug": "^4.4",
+"symfony/error-handler": "^5.0",
```

<br>

And some package **were split into more smaller ones**:

```diff
-"symfony/security": "^4.4",
+"symfony/security-core": "^5.0",
+"symfony/security-http": "^5.0",
+"symfony/security-csrf": "^5.0",
+"symfony/security-guard": "^5.0",
```

## 5. Rector, ECS, and PHPStan

These were production dependencies, but what about dev ones?
Both have the same rules - they need to allow Symfony 5 installation.

The safest way is to use [prefixed versions](https://github.com/rectorphp/rector/issues/177), which don't care about a Symfony version:

```diff
-"phpstan/phpstan": "^0.11",
+"phpstan/phpstan": "^0.12",
-"rector/rector": "^0.5",
+"rector/rector-prefixed": "^0.6",
```

[Easy Coding Standard](https://github.com/symplify/easy-coding-standard):

```diff
-"symplify/easy-coding-standard": "^0.11",
+"symplify/easy-coding-standard": "^0.12",
```

<br>

Update your `composer.json` to include a package that you need.

Then run:

```bash
composer update
```

Still conflicts?

## 6. And The Biggest Symfony Upgrade Blocker is...

If you don't do open-source, you probably don't use the `git tag` feature. It seems that the tagging of a package is a very difficult process. Even packages with million downloads/month had the latest 15 months ago.

### What are Tags For?

Let's say you want to use `symplify/easy-coding-standard` that supports Symfony 5. Here is the deal:

- the latest `symplify/easy-coding-standard` version 6 doesn't support it
- `symplify/easy-coding-standard` dev-master (~= what you see on GitHub) supports it
- but it's not tagged yet and composer forbids to install dev version; e.g. [sentry-symfony](https://github.com/getsentry/sentry-symfony) at time of writing this post
- so you'd have to require its dev version and force composer to install it

```json
{
    "require-dev": {
        "symplify/easy-coding-standard": "dev-master"
    },
    "prefer-stable": true,
    "minimum-stability": "dev"
}
```

- it's a hackish solution, but it *somehow* works

Now imagine one of your package you require requires some other package, that requires another package, that doesn't allow Symfony 5 in tagged version, but in `master`. Well, you've just finished.

That's why it's very important to know to tag a package regularly:

```bash
git tag v2.0.0
git push --tags
```

That's all! Still, many packages support Symfony 5 in the master but are not tagged yet... to be true, not once for the last 2 years. Why? **The human factor**, maintainers are afraid of negative feedback, of back-compatibility breaks, lack of test coverage takes their confidence, etc.

## Tagging Cancer of PHP Ecosystem

These packages block Symfony 5 upgrade for months:

- [stof/gedmo extension](https://github.com/stof/StofDoctrineExtensionsBundle/releases) (last release in 2017)
- [knplabs/doctrine-behaviors](https://github.com/KnpLabs/DoctrineBehaviors/releases) (last stable release in 2018)
- [behat/transliterator](https://github.com/Behat/Transliterator/releases) (last release in 2017) - this [comment sums it up very nicely](https://github.com/Behat/Transliterator/pull/29#issuecomment-567873541)

<div class="text-center">
    <img src="/assets/images/posts/2019/symfony5_nice_comment.png">
</div>

### United We Stand

This will be resolved in the future by an open-source monorepo approach, but we're not there yet.

In the meantime, please **complain at issues**, ask for help and **offer to become maintainer** until it changes (or until somebody forks it and tags the fork).

One good example for all - I complained and offered help at `knplabs/doctrine-behaviors`, got maintainer rights in 3 hours and [made + merged 30 pull-request in the last month](https://github.com/KnpLabs/DoctrineBehaviors/pulse/monthly).

You see, it works :)

## 7. Still Conflicts?

Ok, so you have the right version of packages, everything is stable and allows Symfony 5. Yet still, the composer says "conflicts, cannot install x/y".

To my surprise, the composer is very bad at solving complex conflicts. Composer **reports false positive and blocks your install**, because of installing packages in `/vendor` or overly strict `composer.lock`. I spent 30-60 minutes trying to figure out what the heck is conflicting on every Symfony training I had in the last 2 months. Now I'm able to do it in 3 minutes.

**How?**

- remove `/vendor`
- remove `composer.lock`
- run `composer update`

It works so well I do it more often than resolving conflicts manually.


## 8. Cleanup `bundles.php`

- Doctrine Cache was only [released for Symfony 4.4 and is not supported for any further version](https://github.com/doctrine/DoctrineCacheBundle/releases/tag/1.4.0).

```diff
 return [
-    Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class => ['all' => true],
 ];
```

Switch the dead gedmo/stof doctrine extensions for the maintained [KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors). I'll write a standalone post about this migration, once a stable version is out (check me, pls :)).

```diff
 return [
-    Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle::class => ['all' => true],
 ];
```

We also had some troubles with Switfmailer Bundle:

```diff
 return [
-    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
 ]
```

The [Mailer component](https://symfony.com/blog/new-in-symfony-4-3-mailer-component) will take over Swiftmailer in the future, so this is just a start.

## 9. Clear `config/packages`

```diff
 # config/packages/framework.yaml
 framework:
     ...
-    templating:
-        engines: ["twig"]
```

Don't forget to remove all extension configs. In our case it was:

```diff
-config/packages/stof_doctrine_extensions.yaml
-config/packages/swiftmailer.yaml
-config/packages/dev/swiftmailer.yaml
-config/packages/test/swiftmailer.yaml
-config/packages/twig_extensions.yaml
-config/routes/dev/twig.yaml
```

Small update of the EasyAdmin bundle:

```diff
 # config/routes/easy_admin.yaml
 easy_admin_bundle:
-    resource: '@EasyAdminBundle/Controller/AdminController.php'
+    resource: '@EasyAdminBundle/Controller/EasyAdminController.php'
```

<blockquote class="blockquote text-center">
    And that's all folks!<br>
    Got any questions?
</blockquote>

<div class="text-center">
    All the know-how is taken from practical pull-request, that was under strict Travis control:

    <br>
    <img src="/assets/images/posts/2019/symfony5_pr.png" class="img-thumbnail">
    <br>

    Feel free to explore it, ask, read comments or share your problems.
    <br>

    <a href="https://github.com/pehapkari/pehapkari.cz/pull/243/files" class="btn btn-dark mb-5 mt-3">
        Check the PR on Github
    </a>
</div>
<br>

## Have we Missed Something?

Of course, we did! Every application has a different set of *blocking* dependencies and different sets of used Symfony features that might have changed.

Share your issues in comments or edit this post on Github to make list complete!

<br>

Happy coding!
