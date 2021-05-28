---
id: 44
title: "PHP Object Calisthenics Made Simple - Version 3.0 is Out Now"
perex: |
    Object Calisthenics are 9 language-agnostic rules to help you write better and cleaner code.
    They help you to get rid of "else" statements, method chaining, long classes or functions, unreadable short names and much more.
    <br><br>
    <strong>Object Calisthenics 3.0 runs on CodeSniffer 3.0 and PHP 7.1. It brings 6 of them with fancy configuration and code examples</strong>.
tweet: "What are #php Object Calisthenics and how to use them? #codingStandard #solid"

deprecated_since: "December 2018"
deprecated_message: |
    In time and after years of use, these rules seems rather "academic". They're not helpful and shifts developer's focus too close to each code character. **They need to have broader overview of code as whole instead.**

    **Nowadays I shifted to 1 much better metric - [Cognitive Complexity](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/).**

updated_since: "August 2020"
updated_message: |
    Updated with **ECS 5**, Neon to YAML migration and `checkers` to `services` migration.<br>
    Updated ECS YAML to PHP configuration since **ECS 8**.
---

If you are a coding standard nerd like me, you'll probably have more than just PSR-2 standard in your ruleset. But even if you don't, [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) is a developer-friendly game changer for your code.

## Much Simpler than 2.0

**1. You don't have to know a single thing about coding standards to start**

**2. Simple to install**

```bash
composer require object-calisthenics/phpcs-calisthenics-rules
```

**3. You can start with 1 sniff**

Quick quiz: what is this variable?

```php
$this->di->...;
```

*Dependency Injection? Dependency Injection Container?* Who would guess it's *Donation Invoice*!

**[Rule #6 - Do Not Abbreviate](https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate)** checks these cases. **It detects short names that are ambiguous and hard to decode.**

For a quick check, just run the full config:

```bash
vendor/bin/ecs check src --config vendor/object-calisthenics/phpcs-calisthenics-rules/config/object-calisthenics.yml
```

You can run this locally or put to your CI and you are ready to go.

## Fancy Readme With Examples

We put lots of work to README for the new release. It isn't a long text describing what exactly the rule does and how it originated - there is already [a blog post for that](http://williamdurand.fr/2013/06/03/object-calisthenics).

Instead, README goes right to the point:

- **YES and NO code snippets**,
- **how to use them** - copy/paste CLI command
- and **how to configure them** - link to particular config lines

<img src="/assets/images/posts/2017/object-calisthenics/rule6.png" class="img-thumbnail">

### Configure What You Need

As you can see in the bottom part of screenshot, most of rules are configurable. It allows you **to adapt their strictness to your specific needs and needs of your project**.

*Do you prefer to require min 4 chars?*

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set('ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff')
        ->property('minLength', 4);
};
```

*Do you want to add own allowed short names?*

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // Rule 6: Do not abbreviate
    $services->set(ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff::class)
        ->property('minLength', 4)
        // default: ["i", "id", "to", "up"]
        ->property('allowedShortNames', ['y', 'i', 'id', 'to', 'up']);
};
```

### Minitip: What Can You Configure in Particular Sniff?

- Open `ElementNameMinimalLengthSniff` class in your IDE.
- **Look for public properties**. CodeSniffer uses them to set any configuration.

That was Rule 6.

## 5 more Rules

### 1. Only X Level of Indentation per Method

❌

```php
foreach ($sniffGroups as $sniffGroup) {
    foreach ($sniffGroup as $sniffKey => $sniffClass) {
        if (! $sniffClass instanceof Sniff) {
            throw new InvalidClassTypeException;
        }
    }
}
```

✅

```php
foreach ($sniffGroups as $sniffGroup) {
    $this->ensureIsAllInstanceOf($sniffGefroup, Sniff::class);
}

// ...
private function ensureIsAllInstanceOf(array $objects, string $type)
{
    // ...
}
```

### 2. Do Not Use "else" Keyword

❌

```php
if ($status === self::DONE) {
    $this->finish();
} else {
    $this->advance();
}
```

✅

```php
if ($status === self::DONE) {
    $this->finish();
    return;
}

$this->advance();
```

### 5. Use Only One Object Operator (`->`) per Line

❌

```php
$this->container->getBuilder()->addDefinition(SniffRunner::class);
```

✅

```php
$containerBuilder = $this->getContainerBuilder();
$containerBuilder->addDefinition(SniffRunner::class);
```

### 7. Keep Your Classes Small

❌

```php
class SimpleStartupController
{
    // 300 lines of code
}
```

✅

```php
class SimpleStartupController
{
    // 50 lines of code
}
```

❌

```php
class SomeClass
{
    public function simpleLogic()
    {
        // 30 lines of code
    }
}
```

✅

```php
class SomeClass
{
    public function simpleLogic()
    {
        // 10 lines of code
    }
}
```

❌

```php
class SomeClass
{
    // 20 properties
}
```

✅

```php
class SomeClass
{
    // 5 properties
}
```

❌

```php
class SomeClass
{
    // 20 methods
}
```

✅

```php
class SomeClass
{
    // 5 methods
}
```

### 9. Do not Use Getters and Setters

Classes should not contain public properties.

❌

```php
class ImmutableBankAccount
{
    public $currency = 'USD';
```

✅

```php
class ImmutableBankAccount
{
    private $currency = 'USD';
```

Method should represent behavior, not set values.

❌

```php
    private $amount;

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }
}
```

✅

```php
    private $amount;

    public function withdrawAmount(int $withdrawnAmount)
    {
        $this->amount -= $withdrawnAmount;
    }
}
```

Check [the README](https://github.com/object-calisthenics/phpcs-calisthenics-rules#implemented-rule-sniffs) to see how to use them and configure them.


## 9 &ndash; 6 = 3... Where is the Rest of Rules?

There are 3 more rules to complete the list from [the original manifesto](https://www.amazon.com/ThoughtWorks-Anthology-Technology-Innovation-Programmers/dp/193435614X):

3. Wrap Primitive Types and Strings
4. Use First Class Collections
8. Do Not Use Classes With More Than Two Instance Variables

They are mostly related to DDD ([Domain Driven Design](https://github.com/dddinphp)), [too strict to use in practise or too vague to cover them with semantic rule](https://github.com/object-calisthenics/phpcs-calisthenics-rules#not-implemented-rules---too-strict-vague-or-annoying).

## Thanks to all Contributors

Last but not least, I'd like to personally thank contributors who helped to make this version happen as it is:

- [@frenck](https://github.com/frenck)
- [@mihaeu](https://github.com/mihaeu)
- [@roukmoute](https://github.com/roukmoute)
- [@UFOMelkor](https://github.com/UFOMelkor)

Without your help, this would not have been possible.
Thank you guys.


## To Sum up Object Calisthenics 3.0

- **Simple setup over boring academic explanations**.
- Rules are *explained in examples*.
- 0-setup. You can just **copy-paste CLI commands and run it**.
- You can choose a **level of strictness that suits you** thanks to flexible configuration.


To find more details about this release see [Release notes on Github](https://github.com/object-calisthenics/phpcs-calisthenics-rules/releases/tag/v3.0.0).
