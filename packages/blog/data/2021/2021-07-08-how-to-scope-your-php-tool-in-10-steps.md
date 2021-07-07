---
id: 327
title: "How to Scope Your PHP Tool in 10&nbsp;Steps"
perex: |
    Do you know PHPStan, ECS, [Monorepo Builder](https://github.com/symplify/monorepo-builder), PHPUnit, [Config Transformer](https://github.com/symplify/config-transformer) or Rector?
    <br><br>
    In the previous post, we explored [why are these tools scoped](/blog/why-do-we-scope-php-tools), where scoping makes sense and where not so much.
    <br><br>
    **Do you maintain a PHP tool that runs in the command line**? Today we'll look at 10 steps on how you can scope it too.

tweet: "New Post on the 🐘 blog: How to Scope Your PHP Tool in 10 Steps"
---

## 1. Add php-scoper

[php-scoper](https://github.com/humbug/php-scoper) is a tool that scans our project and its `/vendor`. Then it adds a unique random prefix to every class:

```diff
-namespace Symfony\Component\Console\Command;
+namespace Scoper12345\Symfony\Component\Console\Command;

-use Symfony\Component\Console\Input\InputInterface;
+use Scoper12345\Symfony\Component\Console\Input\InputInterface;

 class Command
 {
     protected function execute(InputInterface $inputInterface,  ...)
     {
     }

     // ...
 }
```

We can install php-scoper as composer dependency. But soon, we'll get into a situation when php-scoper scopes itself and becomes part of the project. We don't want that.

<br>

It's safer to **get a php-scoper PHAR file**, the similar way we use composer as a PHAR file:

```bash
wget https://github.com/humbug/php-scoper/releases/download/0.14.0/php-scoper.phar -N --no-verbose

# then we have a file ready to run
php-scoper.phar ...
```

## 2. Configure php-scoper

Php-scoper needs a configuration file, by convention, named `scoper.php`. It's a file that returns an array.

* the keys = configuration names
* the values = settings value

The simpler this file is the better - e.g. this is how this config look [like in PHPUnit](https://github.com/sebastianbergmann/phpunit/blob/master/build/config/php-scoper.php):

```php
# scoper.php
return [
    'whitelist' => [
        'PHPUnit\*',
    ],
];
```

<br>

How would such configuration look like for the`Symplify\MonorepoBuilder` tool? First, we need to **whitelist namespace of our tool**.

Why? For 2 reasons:

* to allow extension of core files, e.g., every test case extends from `PHPUnit\Framework\TestCase` and not from  `Scoper12HK32J2\PHPUnit\Framework\TestCase`
* it's safe because people only require our package once

```php
# scoper.php
return [
    'whitelist' => [
        'Symplify\MonorepoBuilder\*',
    ],
    'patchers' => [
        // callback to process files after the scoping; we'll use them soon
    ]
];
```

## 3. Add Symfony Specials to php-scoper Config

Now, the php-scoper does a lot of work for us. Yet, it does not understand some framework-specific situations. E.g. Symfony autodiscovery in `config/config.php`:

```php
use Scoper12HK32J2\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    $services = $containerConfigurator->services();
    $services->load('Scoper12HK32J2\\Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
};
```

What is wrong with this file? In config above, we've excluded `Symplify\MonorepoBuilder\` namespace from being scoped. Yet, here it is scoped:

```php
$services->load('Scoper12HK32J2\\Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
```

<br>

That lead to a bug, when Symfony is loading services that do not exist. We need to fix it:

```diff
-$services->load('Scoper12HK32J2\\Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
+$services->load('Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
```

We could do it manually on every scoping, or we can teach `scoper.php` to do it for us via `"patchers"` configuration.


Now, this is the hardest part of the php-scoper configuration, so get ready for callables without types:

```php
# scoper.php
use Nette\Utils\Strings;

return [
    // scope symfony configs
    'whitelist' => [
        'Symplify\MonorepoBuilder\*',
    ],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            // $filePath is sometimes relative, sometimes absolute
            // so always compare the file path with file ends or a regex
            if (! str_ends_with($filePath, 'config/config.php')) {
                // we only care about config/config.php file here
                // if it's anything else, just keep the origin $content
                return $content;
            }

            // remove the prefix
            return Strings::replace(
                $content,
                '#load\(\'' . $prefix . '\\\\Symplify\\\\MonorepoBuilder#',
                'load(\'' . 'Symplify\\MonorepoBuilder',
            );
        },
    ]
];
```

Callables in the `patchers` key have every scoped file on the input. The file is **already scoped** so that we can remove unwanted prefixes.

Our callable above has one job - it finds `config/config.php`, then it will remove the prefix on `load()` method:

```diff
-$services->load('Scoper12HK32J2\\Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
+$services->load('Symplify\MonorepoBuilder\\', __DIR__ . '/../src');
```

That's it. Now the Symfony autodiscovery works again.

## 4. Scope other Public API of Your Project

We've already unscoped the `Symplify\MonorepoBuilder\` namespace. The Monorepo Builder provides an interface that developers can implements, register in the `monorepo-builder.php` config, and the tool will collect it. It looks like this:

```php
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorkerInterface;
use PharIo\Version\Version;

final class SomeReleaseWorker implements ReleaseWorkerInterface
{
    public function work(Version $version)
    {
        // ...
    }
}
```

Can you see the problem?

<br>

The `PharIo\Version\Version` is scoped to `ScoperDF0239\PharIo\Version\Version` and does not exist. If we try to implement this interface, we will crash:

<a href="https://github.com/symplify/symplify/issues/3388#issuecomment-875301853">
    <img src="https://user-images.githubusercontent.com/53906348/124706025-eb8adf00-def6-11eb-8f70-e596e759ae3f.png" class="img-thumbnail">
</a>

**The `PharIo\Version\` namespace is part of our public API.** Meaning people can use it because our interface encourages it.

There are 2 solutions to this problem. The first one is more cleaner, but also more work and BC break:

* wrap `PharIo\Version\Version` in our custom namespaced object, making it a private API

```diff
 use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorkerInterface;
-use PharIo\Version\Version;
+use Symplify\MonorepoBuilder\Version\Version;

 final class SomeReleaseWorker implements ReleaseWorkerInterface
 {
     public function work(Version $version)
     {
         // ...
     }
 }
```

* add `PharIo\Version\*` to unscoped namespace in `scoper.php`

```diff
 # scoper.php

 return [
     // scope symfony configs
     'whitelist' => [
         'Symplify\MonorepoBuilder\*',
+        'PharIo\Version\*',
     ],
     // ...
 ];
```

This way, the `PharIo\Version\*` classes will be skipped from scoping.

I've picked the latter to save trouble for myself and avoid BC break.

<br>

Do you have some public API namespace that developers using your package will try to use? Exclude it in the `'whitelist'` key. Mind the asterisk in the end `\*` - it covers all classes in the namespace.

## 5. Run php-scoper

We have the config ready. Now it's time to run the scoper.

* Include directories with PHP code, bin directory, configs, YAML/NEON/XML files, and the `/vendor` directory.
* Skip the tests and fixtures for tests.

```bash
$RESULT_DIRECTORY=monorepo-builder-scoped

php-scoper.phar add-prefix bin config src vendor composer.json --output-dir "../$RESULT_DIRECTORY" --config scoper.php --force --ansi
```

Now we have scoped tool in the `monorepo-builder-scoped` directory, good job!

## 6. Create Release Repository separated from Develop Repository

So how do we get the scoped version to our users? We have to set up repository architecture first.

The `symplify/monorepo-builder` package is developed in:

* https://github.com/symplify/symplify

The scoped version is published in:

* https://github.com/symplify/monorepo-builder

## 7. Push Scoped Project to Release Repository

The same way we push commits to our repository, we will push scoped code to the remote scoped repository:

```bash
cd monorepo-builder-scoper

# add a remote repository
git init
git remote add origin git@github.com:symplify/monorepo-builder.git

# add content and push it
git add .
git push -f
```

## 8. Include `/vendor` Directory

The most common mistake I make is missing the `/vendor` directory. I scope the project, make it run locally. Everything is fine. Then I push the whole project without its vendor, and it breaks. So don't forget to allow pushing the scoped `/vendor` too:

```diff
# .gitignore
-/vendor
 composer.lock
```



## 9. Automate Scoping Within CI

The whole process above is daunting and error-prone. It must be automated and running in CI, so we know the instant any of our commits break it.

We have it in Symplify GitHub Actions [up and running here](https://github.com/symplify/symplify/blob/main/.github/workflows/build_monorepo_builder_prefixed.yaml). The scoping process [starts in step 4](https://github.com/symplify/symplify/blob/9a8daa14615980b8e03c7970aeadf248b31c7c41/.github/workflows/build_monorepo_builder_prefixed.yaml#L60):





## 10. Learn by Copying from Existing Projects

The best way to learn is to copy already working processes. It's essential to have a safety net if you're doing something the first time, just before you have an a-ha moment and everything clicks together.

That's why you can learn of the full scoping in `symplify/monorepo-builder` that is spread across 3 files.

* [Scoping GitHub Action Workflow](https://github.com/symplify/symplify/blob/main/.github/workflows/build_monorepo_builder_prefixed.yaml)
* [Bash for Scoping and local testing](https://github.com/symplify/symplify/blob/main/packages/monorepo-builder/build/build-monorepo-builder-scoped.sh)
* [`scoper.php` configuration](https://github.com/symplify/symplify/blob/main/packages/monorepo-builder/scoper.php)

<br>

Keep it up, and I look forward to your first scopes!

<br>

Happy coding!
