---
id: 279
title: "Symfony AutoBind Parameter is Dead, Long live Constant Parameters"
perex: |
    I wrote the [Do you Autowire Services in Symfony? You can Autowire Parameters Too](/blog/2018/11/05/do-you-autowire-services-in-symfony-you-can-autowire-parameters-too/) almost 2 years ago. It seemed like a good idea at that time, to save manual YAML config wiring.


    Now, with [PHP configs](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/) on the Symfony markets, auto bind parameters became obsolete.


    Welcome **constant parameters**.

---

In the past, we used YAML to define parameters for Symfony application:

```yaml
parameters:
    location: Prague
```

How do we get parameter in a service? Via name auto binding: **the parameter name = the param name in `__construct`**:

```php
final class SomeService
{
    private string $location;

    public function __construct(string $location)
    {
        $this->location = $location;

        dump($location); // "Prague"
    }
}
```

This allows us to have a typed parameter right in the constructor. We know it's a `string`.

## YAML Without Knowledge

- But where is the `location` param defined?
- Where are all the places the parameter is used in?
- Is the `string $location` in the `__construct` really a constructor parameter? What if that's a value object?

```php
$valuesObjects = [
    new SomeService('Prague'),
    new SomeService('Berlin'),
    new SomeService('London'),
];
```

We don't know. We can be either anxious about it or not care about this detail at all.

<br>

Or we could **quickly know with 1 click in PHPStorm**. How?

## Welcome Constant Parameters

With [Symfony configs in `*.php` format](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs), this is easypick:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('location', 'Prague');
};
```

Wait, this was just a string. We want **constant parameters**:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use App\Configuration\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::LOCATION, 'Prague');
};
```

## When Constant Parameter and Service Parameter Meet

We still want to avoid service configuration... we have 2 options.

- A. Use Symfony [parameter bag](https://symfony.com/blog/new-in-symfony-4-1-getting-container-parameters-as-a-service):

```php
use App\Configuration\Option;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class SomeService
{
    private string $location;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        // re-type to (string) is needed, because "get()" return mixed type
        $this->location = (string) $parameterBag->get(Option::LOCATION);
    }
}
```

- B. Use Symplify [ParameterProvider](https://github.com/symplify/package-builder#get-all-parameters-via-service):

```php
use App\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SomeService
{
    private string $location;

    public function __construct(ParameterProvider $parameterProvider)
    {
        // no re-type needed + parameter type validation included inside the ParameterProvider service
        $this->location = $parameterProvider->provideStringParameter(Option::LOCATION);
    }
}
```

Both services work to find first is out of the box, second is mutable and useful for testing or post-container configurations. Pick the one that suits your project better.

**Are you unsure?** Use the Symfony `ParameterBagInterface`.



## One More Thing...

We now have a clean design and constant parameters without any name <=> name hacking. That's nice.

But the best is yet to come. In the previous version, we were missing essential information about the parameter:

<blockquote class="blockquote text-center mt-5 mb-5">
    Where are the places the parameter is used in?
</blockquote>

<br>

Well, why not just **click on the constant** in your IDE?

<br>

<img src="/assets/images/posts/2020/constant_parameter_locations.gif" class="img-thumbnail">

<br>

Happy coding!
