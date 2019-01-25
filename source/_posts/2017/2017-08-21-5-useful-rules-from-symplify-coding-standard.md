---
id: 51
title: "5 Useful Rules From Symplify Coding Standard"
perex: |
     <a href="https://github.com/Symplify/CodingStandard">Symplify Coding Standard</a> was born from Zenify, back from the days I was only Nette programmer. It focuses on <strong>maintainability and clean architecture</strong>. I try to make them simple: <strong>each of them does one job</strong>.
     <br><br>
     With over 108 000 downloads I think I should write about 5 of them you can use in your projects today.
tweet: "Add Final Interface, Class Constant fixer and more to your Coding Standard #php #architecture #php_codesniffer"

updated_since: "December 2018"
updated_message: |
    Updated with <strong>EasyCodingStandard 5</strong>, Neon to YAML migration and <code>checkers</code> to <code>services</code> migration.

deprecated_since: "September 2018"
deprecated_message: |
    [Symplify 5.0](https://github.com/Symplify/Symplify/tree/v5.0.0) was released and with that, many checkers were replaced by better ones.  
    
    Checkers 2, 4 and 5 were replaced by `SlamCsFixer\FinalInternalClassFixer` - **class is either final or abstract**.  
    
    `@inject` refactoring was replaced by `AnnotatedPropertyInjectToConstructorInjectionRector` from [Rector](https://github.com/rectorphp/rector).
---

I wrote about [Object Calisthenics](/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/) few weeks ago - they are very strict and not very handy if you're beginner in coding standard worlds.

**Symplify Coding standard is complete opposite.** You can start with 1st checker today and your code will be probably able to handle it. It's combination of 23 sniffs and fixers.

The simplest would be...

### 1. Array property should have default value `[]` to prevent undefined array issues

<em class="fas fa-lg fa-times text-danger"></em>


```php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples;

    public function run()
    {
        foreach ($this->apples as $mac) {
            // ...
        }
    }
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples = [];
}
```

**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Property/ArrayPropertyDefaultValueFixer: ~
```

### 2. Final Interface

Once I read [When to declare classes final](https://ocramius.github.io/blog/when-to-declare-classes-final) by [Marco Pivetta](http://ocramius.github.io/) with **tl;dr;**:

*Make your classes always final, if they implement an interface, and no other public methods are defined.*

I was working at [Lekarna.cz](https://www.lekarna.cz/) in that time (finally shipped in the beginning of August, congrats guys!) and we used a lot of interfaces and had lots of code reviews. **So I made a sniff to save us some work.**

<em class="fas fa-lg fa-times text-danger"></em>

```php
class SomeClass implements SomeInterface
{
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
final class SomeClass implements SomeInterface
{
}
```

**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff: ~
```


### 3. Class constant fixer

**Are you on PHP 5.5?** I hope you're [PHP 7.1](/blog/2017/06/05/go-php-71/) already.

Well, since PHP 5.5, you can use `::class` constant instead of string.

<em class="fas fa-lg fa-times text-danger"></em>

```php
$className = 'DateTime';
```

<em class="fas fa-lg fa-check text-success"></em>

```php
$className = DateTime::class;
```


**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer: ~
```

### 4. Test should be final

This is lighter version of **Final Interface rule**. No brainer.

<em class="fas fa-lg fa-times text-danger"></em>

```php
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
}
```

**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\PHPUnit\FinalTestCaseSniff: ~
```


### 5. Equal Interface

What happens if you implement and interface and add few extra public methods?

**Your IDE autocomplete won't work**, if you don't type hint the class and not the interface.

David Grudl recently wrote about [`$template` methods suggestion in Nette](https://phpfashion.com/phpstorm-a-napovidani-nad-this-template).

This sniff helps you to avoid such cases:

<em class="fas fa-lg fa-times text-danger"></em>

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }

    public function extra(): void
    {
    }
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }
}
```


**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff: ~
```


## Experimental Bonus: Refactoring Sniff

`@inject` annotations in Nette have their use cases, but **they are mostly overused and breaking SOLID principles** left and right from my consultancy experience.

Putting annotations back to constructor is quite a work, but this Fixer will help you with that.

<em class="fas fa-lg fa-times text-danger"></em>

```php
class SomeClass
{
    /**
     * @inject
     * @var RequiredDependencyClass
     */
    public $requiredDependencyClass;
}
```

<em class="fas fa-lg fa-check text-success"></em>

```php
class SomeClass
{
    /**
     * @var RequiredDependencyClass
     */
    private $requiredDependencyClass;

    public function __construct(RequiredDependencyClass $requiredDependencyClass)
    {
        $this->requiredDependencyClass = $requiredDependencyClass;
    }
}
```

**Use it**

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\DependencyInjection\InjectToConstructorInjectionFixer: ~
```

### Sold? Try them

They are used the best with [EasyCodingStandard](/blog/2017/08/07/7-new-features-in-easy-coding-standard-22/):

```bash
composer require --dev symplify/easy-coding-standard symplify/coding-standard
```

Check your code:

```bash
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify.yml
```

Fix your code:

```bash
vendor/bin/ecs check --config vendor/symplify/easy-coding-standard/config/symplify.yml --fix
```

Let me know how much errors will you find in the comments. I dare you to get to 0! :)

## Rest of the Rules

You can find more rules like Abstract Class, Exception, Trait and Interface naming, indexed array indentation, Controllers with 1 method or invoke and so on in [README](https://github.com/Symplify/CodingStandard).

Happy coding!
