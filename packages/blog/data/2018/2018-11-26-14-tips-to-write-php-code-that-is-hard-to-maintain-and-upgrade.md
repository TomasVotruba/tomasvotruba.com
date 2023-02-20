---
id: 162
title: "14 Tips to Write PHP Code that is Hard to Maintain and Upgrade"
perex: |
    Today I'll show you how to own your company. All you need to do is write code that no-one can read, is hard to refactor and creates technical debt. It's not easy, because if other programmers spot you're writing legacy code, you're busted.


    If you keep a low profile of very smart architect and do it right, you'll be the only one in the company who knows what is going on and you'll have a value of gold. Learn how to be **successful living vendor lock**!
tweet: "New Post on My Blog: 14 Tips to Write #PHP Code that is Hard to Maintain and Upgrade (in Examples) #vendorlockin #ast #staticanalysis"
tweet_image: "/assets/images/posts/2018/vendor/omg-naming.gif"
---

### 3 Signs of Living Vendor Lock-In

`/vendor` is a directory in your project with all the packages dependencies. [Vendor lock-in](https://en.wikipedia.org/wiki/Vendor_lock-in) is life-death dependency company on you.
It's like having a baby - **you have to take care of it for the next 18 years** (at least):

<img src="/assets/images/posts/2018/vendor/free-hug.jpg" class="img-thumbnail">

How to make company code depend on you? You want to write a code that static analysis and instant upgrades are very hard to use. Where Rector could help you to turn not-so-bad-code to the modern code base in a matter of few weeks, here will fail hard and the only way out will be greenfield review.

<a href="http://www.osnews.com/story/19266/WTFs_m">
    <img src="/assets/images/posts/2018/vendor/wtf.jpg" class="img-thumbnail">
</a>

But not an obvious way like the right one. The company can't find out! You have to be sneaky as *Eliot* to *E-Corp*.

Here are 15 examples of such **code collected from existing vendor lock-in projects** I reviewed:

## 1. Never use `final`

Programmers want freedom, users desire options. Why setting healthy boundaries, when you can set them free:

```php
<?php declare(strict_types=1);

namespace YourProject;

class PriceCalculator
{

}
```

That way, anyone can extend your class:

```php
<?php declare(strict_types=1);

class BetterPriceCalculator extends PriceCalculator
{

}
```

Also, it's [needed for mocking](https://github.com/cpliakas/git-wrapper/issues/159) and there is no [way around that](https://github.com/dg/bypass-finals). There's not even [8 reasons](https://ocramius.github.io/blog/when-to-declare-classes-final) to use `final` anywhere anytime.

## 2. Use `protected` instead of `private`

What is opened class good for with `private` methods? You need to use `protected` method to be really opened:

```php
<?php declare(strict_types=1);

class PriceCalculator
{
    protected function calculatePrice(Product $product)
    {
        // ...
    }
}
```

Be careful, `public` would be too obvious to anyone who ever heard that SOLID and other might find out what's your real intention.

```php
<?php declare(strict_types=1);

class BetterPriceCalculator extends PriceCalculator
{
    protected function calculatePrice(Product $product)
    {
        return 1000;
    }
}
```

## 3. Use Non-String Method Names

This is tricky for real professionals - I hope you're to look smart in front of your team! If there is a way

```php
<?php declare(strict_types=1);

class PriceCalculator
{
    public function calculateDiscount(Product $product, string $type)
    {
        $methodName = 'calculate' . $type; // great job!

        return $this->$methodName($product);
    }
}
```

`$methodName` is a string - it can be anything, so freedom & dynamic!

```php
<?php declare(strict_types=1);

class ProductController
{
    public function orderProductAction(Request $request)
    {
        $form = new OrderProductForm;
        $form->afterSubmit = [$this, 'processForm']; // great job!

        $form->handle($request);
    }

    public function processForm(Request $request)
    {
        // ...
    }
}

class Form
{
    public $afterSubmit;

    public function handle(Request $request)
    {
        // ...

        call_user_func($this->afterSubmit, $request);
    }
}
```

Why is this so well written? Imagine we're looking at the legacy code - huge code base, 100 of places where the method is used at.
Try to rename `processForm` to `processOrderProduct` - AST is not able to detect method name, all it sees is a string. Instant upgrade impossible and you have to do it all manually, good job!

## 4. Don't Always use `PSR-4`

Classes need to be autoloaded to be parsed to AST. PSR-4 section in `composer.json` solves this easily:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    }
}
```

Damn, now every class is easy to find and respects `file.php` → `Class` naming. **How could you complicate this?**

### Put more Classes into 1 file

```php
<?php declare(strict_types=1);

class FatalException extends Exception
{

}

class ApplicationException extends Exception
{

}

class RequestException extends Exception
{

}
```

You have 1 file instead of 3 - you saved so much disk space!

### Use Small Case Naming

```bash
/app
   /Controller
       ProductController.php
```

↓

```bash
/app
   /controller
       ProductController.php
```

PSR-4 is unable to find this file, great job!

### Don't Load Tests

Let's say your `/app` code is loaded by PSR-4. You also have covered it with tests. If you run instant upgrades, it should actually upgrade tests too. Damn.

If you have this section, drop it:

```diff
-{
-    "autoload-dev": {
-        "psr-4": {
-            "App\\Tests\\": "tests"
-        }
-    }
-}
```

PHPUnit uses its own autoload anyway.

## 5. Use Your Own Autoloader

While you're at it, follow PHPUnit example. And not only PHPUnit. Have you ever wondered why there is missing [`"autoload"` section PHP_CodeSniffer `composer.json`](https://github.com/squizlabs/PHP_CodeSniffer/blob/b53f64e10e41aa754ffa7c11999af1881e6c1780/composer.json)
Make your own autoloader - using `spl_autoload_register` or [nette/robot-loader](https://github.com/nette/robot-loader). That way instant upgrade tools get confused and probably won't work. Good job!

## 6. Hide Your Dependencies in Constructor

```php
<?php declare(strict_types=1);

class PackagistApi
{
    public function getPackagesByOrganization(string $organization): array
    {
        $guzzle = new Guzzle\Client();
        $response = $guzzle->get('https://packagist.org/packages/list.json?vendor=' . $organization);

        // ...

        return $packages;
    }
}

$packagistApi = new PackagistApi;
$shopsysPackages = $packagistApi->getPackagesByOrganization('shopsys');
```

When you send such code a to code-review, you are provoking this comment:

- "`PackagistApi` hides `Guzzle\Client` dependency. Put that into constructor injection"

Busted! But there is a way **to improve your chances to make this pass** and still skip constructor injection:

```php
<?php declare(strict_types=1);

class PackagistApi
{
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = new Guzzle\Client();
    }

    public function getPackagesByOrganization(string $organization): array
    {
        $response = $this->guzzle->get('https://packagist.org/packages/list.json?vendor=' . $organization);

        // ...

        return $packages;
    }
}
```

## 7. Put Different Kinds of Objects to One Directory

Do you love DDD? Everyone loves it! Thanks to DDD you have socially accepted reason to use directory names based on categories:

```bash
/app
    /Controller
        ProductController.php
    /Entity
        Product.php
    /Repository
        ProductRepository.php
```

↓

```bash
/app
    /Product
        ProductController.php
        Product.php
        ProductRepository.php
```

Who needs standards! It's this nice? Now no-one can find any classes by expected directory name.

As a bonus, service auto-discovery is not possible anymore:

```yaml
services:
    App\:
        resource: '../app'
        exclude: '../app/{Entity}'
```

Great job!


## 8. Use Annotations to Define Magic Methods

Now you know how to use strings in method names from tip #3. Let's get this to the next level. I've already used this tip above:

```php
<?php declare(strict_types=1);

$guzzle = new Guzzle\Client();
$guzzle->get('...');
```

What happens when you type `get` method? The IDE will tell you the `get` method exists. You can adapt this pattern to your code too!

Then you rename it with instant upgrade tools, but it will fail. It's not a string... so what the hack?

It's only [an annotation](https://github.com/guzzle/guzzle/blob/8db1967d92f55de1b94b175478ed16a7dfc53a90/src/Client.php#L11-L24):

<img src="/assets/images/posts/2018/vendor/magic-annotation.png" class="img-thumbnail">

You now have a reason to add `__call` integration and combine method names with many more strings. Great job!

## 9. Use Traits with Annotations to Define Magic Methods

You can take this to one more level!

```php
<?php declare(strict_types=1);

class ApiCaller
{
   use GetMethodTrait;
}

/**
 * @method getPackagesByOrganization($organization)
 * @method getPackagesByCategory($category)
 */
trait GetMethodTrait
{
}
```

Goodbye automated typehints and static analysis!

## 10. Don't use a Different Naming to separate `Interface`, `Trait` from `Class`

Oh, I actually made a mistake. Above I wrote `GetMethodTrait`, that might actually help the user and tool to guess it's a trait:

```bash
- ProductInterface
- ProductTrait
- Product
```

We don't want that. Let's take another cool tip from DDD and make them look the same ↓

```bash
- Product
- Product
- Product
```

Now no-one can use Finder to find all traits or interface. Each file has to be parsed now and that's super slow. Good job!

## 11. Use as Short Naming as Possible

Long class names are annoying to read. Just compare yourself:

```/ash
- YamlParser
- YamlFileParser
- LatteTemplateParser
- LatteParser
- XmlParser
```

This is much better ↓

<img src="/assets/images/posts/2018/vendor/omg-naming.gif" class="img-thumbnail">

It also it also increases chances to bother with manual aliasing:

```php
<?php declare(strict_types=1);

use Yaml\Parser;
use Latte\Parser as LatteParser;
use Xml\Parser as XmlParser;

class ProductXmlFeedCrawler
{

}
```

WTF, it's so good!

## 12. Don't use a Different Naming to separate `Abstract` classes

Abstract classes are also classes. Why would make that easier for a reader by stating that in a name? Have you ever seen an `AbstractInterface` or `AbstractTrait`? I did not.

```diff
-abstract class AbstractXmlCrawler
+abstract class XmlCrawler
```
Let them look into to class manually. Time well spent!

## 13. Use Fluent API with Different Return Values

[Fluent interfaces are great](https://ocramius.github.io/blog/fluent-interfaces-are-evil), they save you typing the variable name all over again:

```php
<?php declare(strict_types=1);

class Definition
{
    private $class;

    private $arguments;

    public function setClass(array $class)
    {
        $this->class = $class;

        return $this;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }
}

$definition = (new Definition)->setClass('ProductController')
    ->setArguments(['@Request']);
```

The best thing is, there are more ways to create the same object:

```php
<?php declare(strict_types=1);

$definition = new Definition;
$definition->setClass('ProductController');
$definition->setArguments(['@Request']);
```

How can anyone use that incorrectly now?

## 14. Use Fluent API with Different Return Values

Let's take fluent to the next level - make every method return different object:

```php
<?php

// ...

$rootNode
    ->beforeNormalization()
        ->ifTrue(function ($v) { return !isset($v['assets']) && isset($v['templating']) && class_exists(Package::class); })
        ->then(function ($v) {
            $v['assets'] = array();
            return $v;
        })
    ->end()
    ->children()
        ->scalarNode('secret')->end()
        ->scalarNode('http_method_override')
            ->info("Set true to enable support for the '_method' request parameter to determine the intended HTTP method on POST requests. Note: When using the HttpCache, you need to call the method in your front controller instead")
            ->defaultTrue()
        ->end()
        ->scalarNode('ide')->defaultNull()->end()
        ->booleanNode('test')->end()
        ->scalarNode('default_locale')->defaultValue('en')->end()
        ->arrayNode('trusted_hosts')
            ->beforeNormalization()->ifString()->then(function ($v) { return array($v); })->end()
            ->prototype('scalar')->end()
        ->end()
    ->end()
;
```

<div class="text-center pb-4">
    <em>
      From <a href="https://github.com/symfony/symfony/blob/0d35f97e9b9c27df0d6317e8ae09d5b963dc2916/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/Configuration.php">Symfony/FrameworkBundle configuration</a>.
    </em>
</div>

Fluent API like this is [proven to break PHPStan and thus Rector](https://github.com/phpstan/phpstan/issues/254). Such code is almost impossible to upgrade instantly. The longer the fluent methods, the bigger the damage - great job!

<br>

Do you know more tips to write code that is hard to maintain and upgrade in *not-so-obvious* way? **Let me know in the comment, I'll update the list with them.**

<br>

P.S.: If you love sarcastic posts like this, go check [Eliminating Visual Debt](https://ocramius.github.io/blog/eliminating-visual-debt) by Marco Pivetta or [How to Criticize like a Senior Programmer](/blog/2018/03/19/how-to-criticize-like-a-senior-programmer/).
