---
id: 275
title: "What if We Remove Strings from Symfony Extension Configuration"
perex: |
    You can tell I'm a [huge fan PHP configs](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/). To be honest, I don't care; I'm just extremely lazy.
    <br><br>
    Yet, my laziness got me itching when I see **configuration of extensions**.

tweet: "New Post on #php 🐘 blog: What if We Remove Strings from #symfony Extension Configuration"
tweet_image: "/assets/images/posts/2020/remove_string_extension.png"
---

I like the service configuration provided by Symfony. Typo-proof, everything is autocompleted by IDE, hard to put the wrong argument or make a typo.

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->set(SomeService::class)
        ->args(['some', 'value']);
};
```

This config is a joy to use in IDE. Only the values that can change, like class name or arg value, are strings. **Everything else is API of the modeling tool**, here `ContainerConfigurator` from Symfony. This code is a state of Art.

<br>

But that's not everything we have in our configs. Let's look at a common extension you can found in `config/packages/doctrine.php` in your Symfony project:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('break', [
        'dbal' => [
            'host' => '%env(DATABASE_HOST)%',
            'user' => '%env(DATABASE_USER)%',
            'password' => '%env(DATABASE_PASS)%',
        ],
    ]);
};
```

How do you like this?

I'll share you secret deep from my traumatized mind - this is what I see:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('error', [
        'bug' => [
            'typo' => ' _missing',
            ' problem ' => 'sorry_renamed',
            'forgotten' => 'FALSE POSITIVE',
        ],
    ]);
};
```


<blockquote class="blockquote text-center">
    "Anything that can go wrong, will go wrong.<br>
    In the worst possible order. When you least expect it."
</blockquote>


<br>

## How can we make Extension as Good as Service Registration?

If we look at [`ContainerConfigurator`](https://github.com/symfony/symfony/blob/master/src/Symfony/Component/DependencyInjection/Loader/Configurator/ContainerConfigurator.php), it inherits from abstract class [`AbstractConfigurator`](https://github.com/symfony/symfony/blob/master/src/Symfony/Component/DependencyInjection/Loader/Configurator/AbstractConfigurator.php).

What if we use per-extension Configurator... e.g. `DoctrineConfigurator`?

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->user('%env(DATABASE_USER)%')
        ->password('%env(DATABASE_PASS)%');
};
```

Slightly less space fo bug... Still, I managed to split one there. It can cause a "connection to database rejected" error.

## Environment Variables for Everyone

Have you spotted it?

```diff
 return static function (DoctrineConfigurator $doctrineConfigurator): void {
     $doctrineConfigurator->dbal()
         ->user('%env(DATABASE_USER)%')
-        ->password('%env(DATABASE_PASS)%');
+        ->password('%env(DATABASE_PASSWORD)%');
};
```

- How could we prevent this?
- How can one know the conventions of database env values without studying them?
- What are other ENV values for the database we can use?

[Jan Mikes](https://github.com/JanMikes) shared with me an interesting idea that **removes this problem**:

```php
use DoctrineEnvs;

return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->user('%env(' . DoctrineEnvs::DATABASE_PASSWORD . ')%')
        ->password('%env(' . DoctrineEnvs::DATABASE_PASSWORD . ')%');
};
```

<br>

That seems like too much clutter... can we make it simpler and safes?

```php
use DoctrineEnvParams;

return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->user(DoctrineEnvParams::DATABASE_PASSWORD)
        ->password(DoctrineEnvParams::DATABASE_PASSWORD);
};
```

<br>

In what cases is this the most useful?

<br>

Common key for the "database name" is never the same across database platforms and language integration. Imagine the saved hours and lifes on Docker/Doctrine/CI typos:

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->database('%env(DATABASE_NAME)%');
        // or...?
        ->database('%env(DATABASE_DBNAME)%');
        // or...?
        ->database('%env(DATABASE_DATABASE)%');
        // or...?
        ->database('%env(DB_NAME)%');
};
```

Just don't care and use IDE autocomplete:

```php
use DoctrineEnvParams;

return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->database(DoctrineEnvParams::DATABASE_NAME);
};
```

## What are Benefits over Array configuration?

- **the parameter is validated by standard PHP** - if you put database name with `int`, the PHP throws an exception right on that line

### Focus on Config without Jumping Elsewhere

- the IDE autocompletes method names - **no need to look into configuration**, what can be used - you can stay focused on your code
- the IDE autocompletes ENV names - **no need to look into configs or extension on Github** - you can stay focused on your code

### Narrow Context

- context-aware autocomplete - when you type `dbal()` or `orm()` you only get methods, that are relevant in that context

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->... // dbal specific methods

    $doctrineConfigurator->orm()
        ->... // orm specific methods
};
```

- we can also go to a more narrow scope, like single `connection()`

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->conntection()
            ->... // only connection specific methods
};
```

The `DoctrineConfigurator` with specific `methods()` and `Constant::KEYS` is one way to get rid of all string possible.

## What about Value Objects?

With PHP 8.0 to be released in 11/2020 will come [named arguments](https://stitcher.io/blog/php-8-named-arguments). With them, the IDE autocomplete becomes more powerful. If we combine it along with `__construct` validation in value objects, we have another solid way to add parameters:

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->connection(new DbalConnection(
            DoctrineEnvParams::DATABASE_USER,
            DoctrineEnvParams::DATABASE_PASSWORD,
            DoctrineEnvParams::DATABASE_NAME
        ));
};
```

Compared to method() autocomplete, we can also see **what arguments are required** and which optional:

```php
// using PHP 8.0 syntax with constructor promotion

final class DbalConnection
{
    public function __construct(
        private string $user,
        private string $password,
        private string $database,
        private ?string $version = null
    ) {
        // ...
    }
}
```

## Instant Feedback Loop

If we get rid of strings that can go wrong, we've made a big shift [to senior codebase](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/).

Another huge benefit for programmers **is focus - along with [instant feedback loop](/blog/2020/01/13/why-is-first-instant-feedback-crucial-to-developers/)**.

<blockquote class="blockquote text-center">
    "If something goes wrong, I want to know it the moment it went wrong"
</blockquote>

- If we put the wrong password to the database, the project works on the localhost server, the tests are passing, and CI is green, our feedback loop is slow, and we have to speed it up.

- If the project crashes on the localhost server and tests are failing, our feedback look is fast.

<br>

**How is instant feedback loop related to value objects and require arguments?** Good question!

If we put user and password but forget the database name, the application will crash **when** it's connected:

```php
use DoctrineEnvParams;

return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->user(DoctrineEnvParams::DATABASE_USER)
        ->password(DoctrineEnvParams::DATABASE_PASSWORD);
};
```

That's very late!

With value objects, we'll get the "missing 3rd argument" error:

```php
return static function (DoctrineConfigurator $doctrineConfigurator): void {
    $doctrineConfigurator->dbal()
        ->connection(new DbalConnection(
            DoctrineEnvParams::DATABASE_USER,
            DoctrineEnvParams::DATABASE_PASSWORD
            // boom :(
        ));
};
```

**That's fast feedback loop!**

<br>

These were few ideas, how to **get rid of strings** in `/config` directory, so we can instead focus on something we love - algorithms, coding, and maybe a coffee cup!

<br>

I bet you can't come up with a better way to do this... share in comments to prove me wrong ;)

<br>

Happy coding!
