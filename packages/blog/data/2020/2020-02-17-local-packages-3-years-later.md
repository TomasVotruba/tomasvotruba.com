---
id: 239
title: "Local Packages 3 Years Later"
perex: |
    The first public idea about local packages was [published over 3 years ago](/blog/2017/12/25/composer-local-packages-for-dummies/) after 1 year of internal testing.
    <br><br>
    **How do they stand in 2020? How people use it wrong?** Are they still the best option to keep low complexity in huge projects?
tweet: "New Post on #php 🐘 blog: Local Packages 3 Years Later"
---

Just a reminder: *what are local packages*?

Local packages are decoupled parts of code, located in own `packages/<package-name>` directory:

```bash
/app
/packages
    /file-system
        /src
            FileSystem.php
        /tests
            FileSystemTest.php
/vendor
composer.json
```

And loaded in `composer.json` with its PSR-4 namespace:

```json
{
    "require": {
        "favorite/framework": "^5.0"
    },
    "autoload": {
        "psr-4": {
            "App\\FileSystem\\": "packages/file-system/src"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\FileSystem\\Tests\\": "packages/file-system/tests"
        }
    }
}
```

Simple.

<br>

Do you want to **know more**? Look at [Composer Local Packages for Dummies](/blog/2017/12/25/composer-local-packages-for-dummies/).

## How People Use it Wrong?

### 1. Forgetting `/src` Directory

```bash
/packages
    /file-system
        FileSystem.php
```

❌

<br>


```bash
/packages
    /file-system
        /src
            FileSystem.php
```

✅

<br>

### 2. Single Autoload

```json
{
    "autoload": {
        "psr-4": {
            "Packages\\": "packages"
        }
    }
}
```

❌

<br>


```json
{
    "autoload": {
        "psr-4": {
            "Packages\\SpecificPackage\\": "packages/specific-package/src"
        }
    }
}
```

✅

<br>

### 3. A mix of Paths and Namespace

```bash
/packages
    /FileSystem
        /src
            FileSystem.php
```

❌

<br>


```bash
/packages
    /file-system
        /src
            FileSystem.php
```

✅


### 4. `composer.json` in Packages

```bash
/packages
    /file-system
        /src
            FileSystem.php
        composer.json
```

❌

<br>


```bash
/packages
    /file-system
        /src
            FileSystem.php
```

This is only useful in case of [monorepo that splits packages](/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command/), e.g. Symfony, Symplify. Not for local packages.

✅


## How to Do it Right?

- Register each package as a standalone line in `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "App\\FileSystem\\": "packages/file-system/src",
            "App\\Auth\\": "packages/auth/src"
        }
    }
}
```

- Keep `/app` separated.
- Use `dash-format` for directory paths.
- Use `CamelCase` for namespaces.
- Use `packages/<package-name>/src` and `packages/<package-name>/tests` directory convention.
- Use single root `composer.json` to autoload them all.
- Use single root `phpunit.xml` to run test on them all.

```xml
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <testsuites>
        <testsuite name="main">
            <directory>tests</directory>
            <directory>packages/*/tests</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist addUncoveredFilesFromWhitelist="false">
            <directory>src</directory>
            <directory suffix=".php">packages/*/src</directory>
        </whitelist>
    </filter>
</phpunit>
```

## Feedback After 3 Years of Usage in Companies

I've started to test this in Lekarna.cz, 6 years old project, where they still use it.
[Elasticr](https://www.elasticr.cz) and [Recruit.is](https://recruitis.io) adopted local packages in ~2018, still using it.

The code is much cleaner, comfortable to dive in, and refactor.

**If you're careful about all the issues above, there is nothing to stop you from making it work!** Give it a try, your future team will thank you.


<br>

Happy coding!
