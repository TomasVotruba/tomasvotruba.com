---
id: 292
title: "New in Symplify 9: Skipper - Skipping Files and Rules made Simple"
perex: |
    Symplify 9 brings another component **to ease the life for package developers**. In Rector and ECS, you can ignore specific absolute paths or dynamic paths by `fnmatch()`. You can also ignore specific Rector, Fixer, and Sniff classes.
    <br><br>
    Both packages use almost the same syntax, yet there are minor differences based and syntax tweaks.
    <br><br>
    New Skipper component prevents this and allows **you to use standardized syntax in a single `skip` parameters**.

tweet: "New Post on #php 🐘 blog: New in #Symplify 9: Skipper - Skipping Files and Rules made Simple"
tweet_image: "/assets/images/posts/2020/skipper-kangaroo.jpg"
---

When I started writing this post, I remembered a TV series from my childhood about the hero kangaroo:

<img src="/assets/images/posts/2020/skipper-kangaroo.jpg" class="img-thumbnail">

It's like Batman, just kangaroo. Anyone? :) Ok, I'm old. Back to the software skipper.

<br>

## "How Can I Skip the ...?"

This is one of the most common questions in Rector or ECS issues. Skip feature is native to most of CLI tools:

- [PHPStan has `ignoreErrors` and `excludes_analyse` parameter](https://phpstan.org/user-guide/ignoring-errors)
- [PHP_CodeSniffer has command line and annotation support](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-files-and-folders)
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/3317) is thinking about it since 2017

Most packages have at least 2 ways to skip a file or rule:

- exclude_files
- excludedFiles
- skip
- ignored
- ignorePaths
- ignoreDirectories
- notAllowedPaths
- ...

It makes sense to have each information separated for the package developer. Then they know if you want to skip a path or a rule. But it's **absolute hell for the user of the package**.

- It's a common case, we need to ignore one file in all our CLI tools.
- For each package, we have to learn new syntax.
- Typos, hard to remember, you skip a class in paths parameters...

We've all been there.

## Complexity opens Space for Bugs

These complicated configuration options also allows to have duplicated skip. Try debugging this:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Skipper\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        SomeSniff::class => [
            __DIR__ . '/some-path',
        ]
    ]);

    $parameters->set(Option::EXCLUDE_RULES, [SomeSniff::class]);
};
````

Why is the `SomeSniff` skipped in `__DIR__ . '/another-path'` too? It shouldn't be, only `__DIR__ . '/some-path'`.

## KISS: The `SKIP` Parameter

Why not take users priority and make it simple for them? Let's use [Occam's razor](/blog/2020/03/09/art-of-letting-go/) and provide intuitive choice:

Single `skip` parameter:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Skipper\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        // absolute directory
        __DIR__ . '/some-path',

        // absolute file
        __DIR__ . '/some-path/some-file.php',

        // with mask
        '*/Fixture/*',

        // specific class
        SomeClass::class,

        // specific class specific path
        NarrowedClass::class => [__DIR__ . '/src/OnlyHere'],

        // class code in paths
        AnotherSniff::class . '.SomeCode' => ['*Sniff.php', '*YamlFileLoader.php'],
    ]);
};
````

That's it!

<br>

That's the main principle of [Skipper](https://github.com/symplify/skipper).
Rector 0.9 and ECS 9 build on Skipper, so be sure to use its syntax in `ecs.php` and `rector.php`. Instead of many `skip`/`exclude_paths`/`exclude_rules` params, there is just `skip` now.

## How to Upgrade from Symplify 8 to 9 and Rector 0.8 to 0.9

Just to be sure, this is how you upgrade your `ecs.php` and `rector.php` configs:

```diff
-$parameters->set(Option::EXCLUDE_PATHS, [
+$parameters->set(Option::SKIP, [
     // paths to skip
     '*/Fixture/*',
     __DIR__ . '/packages/easy-hydrator/tests/Fixture',
-]);
-$parameters->set(Option::SKIP, [
-     ArrayDeclarationSniff::class => null,
+     ArrayDeclarationSniff::class,
 ]);
```

After renaming of `EXCLUDE_PATHS` to `SKIP`, make sure there is just one `SKIP` option in your config. Symfony would pick just one of them without telling you.

<br>

Happy coding!
