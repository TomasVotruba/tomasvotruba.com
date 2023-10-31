---
id: 396
title: "How to Autowire of Multiple-Instances Effortlessly in Symfony/Laravel"
perex: |
    Do you work with Symfony since 2 through 7? Then you know the main challange in the upgrade path is to trim your YAML configs to minimum.

    Where we needed 300 lines in YAML configs, we now need 2 lines. How to get there fast and reliable? I'll show 3 trick we use in Symfony project to get there.
---

Physical industry and software one have much in common and can learn from each other. The process is being automated, the simpler beats complex and simpler in automated process scales squared.

<br>

The same way Tesla used to be build from 170+ pieces,
but now is just **2 pieces** with one smart machine press.


<img src="https://github.com/TomasVotruba/tomasvotruba.com/assets/924196/14e46986-656c-4891-8c90-1d5df0a68144" class="img-thumbnail mt-3 mb-3">

<br>

Narrowing YAML or array dinosaur files to few lines [is an area](https://tomasvotruba.com/blog/run-config-transformer-in-ci-everyday-to-keep-yaml-away) [I wrote](https://tomasvotruba.com/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify) [many times](https://tomasvotruba.com/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs) [before](http://localhost:8000/blog/2019/07/22/how-to-convert-listeners-to-subscribers-and-reduce-your-configs).

<br>

I know that every Symfony project can be loaded with single line of autodiscovery call, if done correctly. Once we've done easy part of autowire and autodiscovery, we can focus on the challenges I'll show you today.

## What is our Goal in First Principles?

* [move away from config coding](/blog/2019/02/14/why-config-coding-sucks)
* use PHP service design as raw as possible

## 1. Same type with Various Configuration

If you ask Symfony/Laravel container for a `PasswordHasher` service and there is exactly one instance in our whole container, it will be autowired.

```yaml
services:
    aws_client:
        class: Some\HttpClient
        arguments:
            $name: tom
            $password: 123

    bank_client:
        class: Some\HttpClient
        arguments:
            $name: john
            $password: 456
```

Here we have 2 instances of `HttpClient` service, but they are the same type.
The proclaimed reason to use config coding here is "to provide manual configuration". We can do better.

<br>

To use one of them, we have to define it explicitly:

```yaml
services:
    another_service:
        - '@aws_client'
```

Here is a challenge: how to tell Symfony/Laravel to autowire them in unique way and **drop the whole configuration**?

<br>

We already know that container works for us, once we ask it for **a unique typed service**. So we create 2 unique classes:

```diff
-Some\HttpClient
+Some\AwsHttpClient

-Some\HttpClient
+Some\BankHttpClient
```

<br>

Now we move the configuration to service itself, we get better:

* **autowiring** - we don't need to define the service in config
* context is where the service is - we no longer use service that is defined in config, the configuration is in the unique service itself
* **IDE support, PHPStan support, Rector support** - we can use PHPStan for type checking, Rector for future migrations and IDE for quick jump between typed files

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


```php
final class BankHttpClient extends HttpClient
{
    public function __construct()
    {
        // static checks right in the code
        parent::__construct('john', '456');
    }
}
```

<br>

Happy coding!
