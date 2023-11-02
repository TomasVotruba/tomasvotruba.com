---
id: 396
title: "How to Autowire Multiple Instances of Same Type in Symfony and Laravel"
perex: |
    Do you work with Symfony from 2 through 7? Then you know the main challenge in the upgrade path is to trim your YAML configs to a minimum.

    **Where we needed 300+ lines in configs, we are now good with 2**. How to get there fast and reliable?

    I'll show a trick we use in the PHP project to get there once and for all.
---

Physical industry and software have much in common and can learn from each other. The process is being automated, and the simpler beats complex and simple in automated process scales squared.

<br>

In the same way, the Tesla car frame used to be built from 170+ pieces,
but now it is just **2 pieces** with one innovative machine press.


<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/14e46986-656c-4891-8c90-1d5df0a68144" class="img-thumbnail mt-3 mb-3">

<br>

Narrowing YAML or array dinosaur files to a few lines [is an area](/blog/run-config-transformer-in-ci-everyday-to-keep-yaml-away) [I wrote](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify) [many times](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs) [before](/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs).

<br>

I know that every Symfony project can be loaded with a single line of autodiscovery calls if done correctly. Once we've done the easy part of autowire and autodiscovery, we can focus on the challenges I'll show you today.

## What is our Goal in First Principles?

* [move away from config coding](/blog/2019/02/14/why-config-coding-sucks)
* use PHP service design as raw as possible

## Same type with Various Configuration

If you ask Symfony/Laravel container for a `PasswordHasher` service and there is exactly one instance in our whole container, it will be autowired.

```yaml
services:
    aws_client:
        class: HttpClient
        arguments:
            $name: tom
            $password: 123

    bank_client:
        class: HttpClient
        arguments:
            $name: john
            $password: 456
```

Here, we have 2 instances of the `HttpClient` service, but they are the same type.
The proclaimed reason to use config coding here is "to provide manual configuration". We can do better.

<br>

To use one of them, we have to define it explicitly:

```yaml
services:
    another_service:
        - '@aws_client'
```

Here is a challenge: how to tell Symfony/Laravel to autowire them uniquely and **drop the whole configuration**?

<br>

## Replace Duplicated Types with Unique One

We already know that container works for us once we ask it for **a unique typed service**. So, we create 2 unique classes:

```diff
-HttpClient
+AwsHttpClient

-HttpClient
+BankHttpClient
```

<br>

Now we move the configuration to the service itself, we get better:

* **autowiring** - we don't need to define the service in the config
* context is where the service is - we no longer use service that is defined in config, the configuration is in the unique service itself
* **IDE support, PHPStan support, Rector support** - we can use PHPStan for type checking, Rector for future migrations, and IDE for quick jump between typed files

```php
final class AwsHttpClient extends HttpClient
{
    public function __construct()
    {
        // static checks right in the code
        parent::__construct('tom', '123');
    }
}
```

<br>

```php
final class BankHttpClient extends HttpClient
{
    public function __construct()
    {
        parent::__construct('john', '456');
    }
}
```

That's it!

<br>

We can now:

* easily use functions like `getenv()` and
* run PHPStan to ensure the values `BANK_NAME` and `BANK_PASSWORD` are defined in our `.env` files

```php
final class BankHttpClient extends HttpClient
{
    public function __construct()
    {
        parent::__construct(getenv('BANK_NAME'), getenv('BANK_PASSWORD'));
    }
}
```

<br>

As a bonus, any PHP DI container will now handle autowire for you, so you can [switch back and forth](/blog/from-symfony-to-laravel-5-steps-to-prepare-your-symfony-project-for-migration) with no config refactorings.

<br>

Happy coding!
