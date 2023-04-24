---
id: 298
title: "4 Ways Symplify makes Working With Symfony More Fun"
perex: |
    Are you new to Symplify? In short, it's a set of packages I wrote that focuses on improving daily PHP development. It brings joy to the code, so you can focus more on what you love.

    Are you a Symplify power user? You still might find some new tricks you didn't know about.

---

<blockquote class="blockquote text-center">
    "If you're just safe about the choices you make,
    <br>
    you don't grow."
</blockquote>

## 1. Is the Annotation available as the Attribute?

<a href="https://github.com/symplify/phpstan-rules" class="btn btn-dark btn-sm mb-1">
    From phpstan-rules package
</a>

Some Symfony annotations are already [available as attributes since Symfony 5.2](https://symfony.com/blog/new-in-symfony-5-2-php-8-attributes), e.g., route:

```diff
 use Symfony\Component\Routing\Annotation\Route;

 class SomeController
 {
-    /**
-     * @Route("/path", name="action")
-     */
+    #[Route(path: '/path', name: 'action')]
     public function someAction()
     {
     }
 }
```

But not everyone is aware of those. Let's try it. Do you know which 61 Validation annotations are available as attributes?
I have no idea, but we have PHPStan for that. Resp. `Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule` class.

**It warns us to be about every annotation usage, where the attribute should be used instead.**

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    /**
     * @Route("/path", name="action")
     */
    public function someAction()
    {
    }
}
```

❌

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route(path: "/path", name: "action")]
    public function someAction()
    {
    }
}
```

✅

<br>

You can configure it yourself or use default setup:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/symfony-rules.neon
    - vendor/symplify/phpstan-rules/config/services/services.neon
```

## 2. Is Parameter in Config used Just Once?

<a href="https://github.com/symplify/phpstan-rules" class="btn btn-dark btn-sm mb-1">
    From phpstan-rules package
</a>

Cyclic dependencies, nested package method calls on mixed types everywhere are the most annoying bugs that take hours to find. Well, until you make a typo or override something, you've defined two lines above.

One of these places is in Symfony PHP configs. If you haven't switched from YAML to PHP, [do it today](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/). Then you can have this bug too:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('secret_hash', 'ASDF1234');

    // this should never happen! it overrides the first param set
    $parameters->set('secret_hash', 'ASFD1234');
};
```

The `secret_hash` should be `ASDF1234`, but it's not. Why?

There is more than just a new kind of bug in your project... well, to be honest, this bug can happen in YAML too, and there is no way to detect it.

In PHP we have a great team member that helps with details that matters - **a PHPStan rule**:

```yaml
rules:
    - Symplify\PHPStanRules\Rules\PreventDoubleSetParameterRule
```

Now you can type configs as you like, and PHPStan will warn you about every duplicated param in your config!

✅


## 3. Autowire List of Services

<a href="https://packagist.org/packages/symplify/autowire-array-parameter" class="btn btn-dark btn-sm mb-1">
    From autowire-array-parameter package
</a>

Let's say you are writing a book, and apart from the text, you have all kinds of resources - images, tool output in txt, PHP code snippets, etc. There are single `BookProcessor` services that collect many `ResourceProcessorInterface`:

- `ImageResourceProcessor`
- `OutputResourceProcessor`
- `PHPSnippetResourceProcessor`

How would you pass all of them to `BookProcessor`?

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $resourceProcessorTag = 'resource_processor';

    $services->instanceof(ResourceProcessorInterface::class)
        ->tag($resourceProcessorTag);

    $services->set(BookProcessor::class)
        ->bind('$resourceProcessors', tagged_iterator($resourceProcessorTag));
};
```

That looks like a valid Symfony code. But what if you register a resource processor in another config or package? Boom, it's missed out. Three hours later, you want to kill yourself. And there [is more problems on the way](/blog/2018/11/12/will-autowired-arrays-finally-deprecate-tags-in-symfony-and-nette/).

We can reduce this boring code to:

```diff
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
- use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

 return function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();
-
-    $resourceProcessorTag = 'resource_processor';
-
-    $services->instanceof(ResourceProcessorInterface::class)
-        ->tag($resourceProcessorTag);
-
-    $services->set(BookProcessor::class)
-        ->bind('$resourceProcessors', tagged_iterator($resourceProcessorTag));
};
```

Much better. How did we do it? With **autowired array** compiler pass:

```diff
+use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
 use Symfony\Component\HttpKernel\Kernel;

 final class AppKernel extends Kernel
 {
     protected function build(ContainerBuilder $containerBuilder): void
     {
+         $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
     }
 }
```

Everything else still looks the same. You've just made your configs more readable and code more robust.

✅

## 4. What is the Doctrine Extension key name in the config?

<a href="https://github.com/symplify/amnesia" class="btn btn-dark btn-sm mb-1">
    From amnesia package
</a>

When you switch to PHP configs, you realize how many kinds of strings there are:

- configuration key
- configuration value
- env value

The first one is always the same. It's defined in the documentation, e.g., for [Doctrine keys](https://symfony.com/doc/current/reference/configuration/doctrine.html). But would you go to documentation and google for a string, or would you use your IDE to help out?

I'm lazy, so I go with the latter one. A typical example is hostname, host... or was it host_name?

<img src="/assets/images/posts/2021/doctrine_host.gif" class="img-thumbnail">

There are few other classes you can use:

- `Symplify\Amnesia\ValueObject\Symfony\Extension\DoctrineExtension`
- `Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\DBAL`
- `Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\ORM`
- `Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\Mapping`

and more.

<br>

This is how we use it on [getrector.com configs](https://github.com/rectorphp/getrector.com/blob/master/config/packages/doctrine.php):

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\DBAL;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\Mapping;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\ORM;
use Symplify\Amnesia\ValueObject\Symfony\Extension\DoctrineExtension;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(DoctrineExtension::NAME, [
        DoctrineExtension::DBAL => [
            DBAL::DRIVER => 'pdo_mysql',
            DBAL::SERVER_VERSION => '5.7',
            DBAL::PASSWORD => env('DATABASE_PASSWORD'),
        ],
        DoctrineExtension::ORM => [
            ORM::MAPPINGS => [
                'demo' => [
                    Mapping::IS_BUNDLE => false,
                    Mapping::TYPE => Mapping::TYPE_ANNOTATION,
                    // ...
                ]
        // ...
```

This way, we can easily see:

- what is a **constant configuration**
- and what is a **manually defined value**

<br>

**Little extras**: have you ever made typo in `%env(...)`?

<img src="/assets/images/posts/2021/doctrine_env.gif" class="img-thumbnail">

Use `env()` function so save the hustle. Thanks enumag for the tip!

That's all for today. I hope you will use some of these tools to become a lazier and happier developer.

<br>

Happy coding!
