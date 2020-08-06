---
id: 269
title: "10 Cool Features You Get after switching from YAML to PHP Configs"
perex: |
    You've probably noticed [Symfony is moving towards PHP configuration](https://github.com/symfony/symfony/issues/37186). If you're on XML or YAML, you'll most likely migrate to PHP with upcoming Symfony 6.
    <br>
    <br>
    There are already tools that [can help you migrate today](https://twitter.com/VotrubaT/status/1283003111074922497) - **so it's not a matter of work, but a matter of choice**.
    <br>
    <br>
    Today we look at 10 cool features you get by switching to PHP configs that make you an even lazier programmer.

tweet: "New Post on #php 🐘 blog: 10 Cool Features You Get after switching from YAML to PHP Configs    #symfony"
tweet_image: "/assets/images/posts/2020/yaml_php_constants.gif"
---

Do you **care why** this begun? Be sure to read [Symfony issue](https://github.com/symfony/symfony/issues/37186):

<blockquote class="blockquote text-center mt-5 mb-5">
    The biggest advantage is IDE auto-completion out of the box,
    <br>
    and one XML/YAML language less to learn.
</blockquote>

<br>

There are the cool features I've observed from migration of **50+ YAML configs to PHP**:


## 1. Absolute Paths over Random Paths

In configs, we often define paths to files or directories. Could you tell me what exact path this is?

```yaml
parameters:
    paths:
        - 'src'
```

- relative to the config location?
- absolute to `%kernel.projectDir%`?
- absolute to `%cwd%`?
- what will warn us if we move the config?

<br>

**We don't know**. It depends on the internal tool implementation and luck.

<br>

Now the same config in PHP:

<img src="/assets/images/posts/2020/yaml_php_absolute_click.gif" class="img-thumbnail">

Pretty clear, right?

## 2. We Can See ~~Deprecated Classes~~ in PHPStorm

The YAML config **will not show you**, if class was deprecated:

<img src="/assets/images/posts/2020/yaml_php_deprecated_class_yaml.png" class="img-thumbnail">

PHP will ~~cross the deprecated class~~ and prepare you better for the future:

<img src="/assets/images/posts/2020/yaml_php_deprecated.png" class="img-thumbnail">


## 3. Missing Classes in Parameters

In YAML, everything is a string by default, so YAML doesn't know that you mean *a class*.

PHP show that pretty clearly:

<img src="/assets/images/posts/2020/yaml_php_missing_class.png" class="img-thumbnail">


## 4. IDE Autocomplete Just Works

Do you recall YAML struggle, when you want to **register a service**?

<img src="/assets/images/posts/2018/symfony-plugin/yaml-class.gif" class="img-thumbnail">

<br>

In PHP config you can [forget it](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock) and just type:

<img src="/assets/images/posts/2020/yaml_php_class_autocomplete.gif" class="img-thumbnail">

Even with typos like "Appication" ;)


## 5. How was that `calls` Syntax?

Do you remember how to set a call on the service setup?

```yaml
services:
    SomeClass:
        calls:
            # what now?
```

No, don't Google it! Try from the top of your head.

<br>

An intuitive way would be to use the same syntax as properties/arguments:

```yaml
services:
    SomeClass:
        calls:
            'setValue': [1]
```

No :( then we have to Google it...

<br>

In PHP we **can use intuitive approach** and see what IDE tells us:

<img src="/assets/images/posts/2020/yaml_php_calls.gif" class="img-thumbnail">


## 6. No More Magic YAML syntax for Constants

In YAML everything is a string:

```yaml
parameters:
    line_ending: PHP_EOL
```

Is that `PHP_EOL` constant as we know it? No, it's `"PHP_EOL"` string.

<br>

How can we specify a **string that is a constant**? (Feels weird for my brain just writing this sentence.)

[Symfony 3.2 introduced special prefix](https://symfony.com/blog/new-in-symfony-3-2-php-constants-in-yaml-files): `!php/const`

```yaml
parameters:
    line_ending: !php/const PHP_EOL
```

Pretty crazy, right?

<br>

How does PHP solve this?

```php
use  Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('line_ending', PHP_EOL);
};
```

**It just works**, no headache!

<br>

## 7. One Way to Add Arguments

These 2 files will produce the same configuration:

```yaml
services:
    SomeService:
        # silent "arguments" key is omitted
        $key: value
```

```yaml
services:
    SomeService:
        arguments:
            $key: value
            # or was it this?
            # key: value
```

<br>

In PHP this is **just 1 clear way**:

```php
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(Application::class)
        ->arg('key', 'value');
};
```

<br>

## 8. No More Magic Tags Without Name

For tags, these 2 files produce the same output:

```yaml
services:
    SomeService:
        tags:
            - kernel.event_subscriber
            # how do we extend this later?
```

```yaml
services:
    SomeService:
        tags:
            - { name: 'kernel.event_subscriber' }
```

In PHP, there is just 1 way for both:

<img src="/assets/images/posts/2020/yaml_php_event_tag.gif" class="img-thumbnail">

## 9. ECS, Rector & PHPStan watches your Config Now

This feature is my favorite by far because it finally opens the door for next-level automation.

YAML doesn't have any static analyzer or instant upgrade tool. This makes the upgrade of Symfony projects double the work - in PHP,  in YAML.

Since `*.php` is PHP code, all the powerful CI tools have access to it.

<br>

**Fewer bugs, effortless changes, less upgrade work, new PHPStan rules, more fun coding.**

## 10. Constants over Strings 🎉

Last but not least. You've probably noticed I hete typoes. I'm so used to tool watching my back, that I type much faster than my fingers can. Then I run the tools and code works (usually).

If you give me a choice of `"string"` or `CONSTANT`, 10 of 10 [I pick the `CONSTANT`](/blog/2020/05/25/the-bulletproof-event-naming-for-symfony-event-dispatcher/) (unless the string is `"really sexy and smart, verified"`).

<br>

**We don't have such a solid choice in YAML**. If we want to use a parameter used elsewhere, we need to trust name is somehow validated (it isn't, because exception will tell you anyway).

<br>

For example, how do you ignore files in ECS?

```yaml
parameters:
    excluded_files:
    # or
    excluded_path:
    # or
    excluded_paths:
```

None of them :(. It's `exclude_files`! **It's painful to look for such bugs** because you need to analyze the whole project every time the config is changed.

<br>

**In PHP, we can do this**:

<img src="/assets/images/posts/2020/yaml_php_constants.gif" class="img-thumbnail">

- Does the constant name change in the future? Rector handles it
- Is the constant missing? PHPStan reports it

## 🚀🚀🚀

<br>

## Start Today, Time passes Anyway

Do you need more real-life examples to try it yourself? Learn from merged pull-requests:

- [`bolt/core` migration of `ecs.yaml` to `ecs.php`](https://github.com/bolt/core/pull/1636)
- [the smallest migration of 1 file](https://github.com/TomasVotruba/tomasvotruba.com/pull/1023/commits/317451fe4770bf5fadd2f5f0807b0dc20c5ad121)
- [`symlify/easy-coding-standard` of 20 sets](https://github.com/symplify/symplify/pull/2012)
- [issue that lists finished YAML to PHP migrations](https://github.com/migrify/migrify/issues/61)

<br>

And of course, don't do it manually. Use **automated tools**:

- [`migrify/config-transformer`](https://github.com/migrify/config-transformer) - handles YAML/XML to PHP/YAML
- [`symfony/maker-bundle`](https://github.com/symfony/maker-bundle/pull/604) - work in progress, handles YAML to PHP

<br>

Happy coding!
