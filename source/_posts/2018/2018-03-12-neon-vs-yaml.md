---
id: 83
title: "Neon vs. Yaml and How to Migrate Between Them"
perex: |
    Do you know `*.neon` format? It's config file format created in Czech Republic by [David Grudl](https://davidgrudl.com/) (author of [Nette](https://github.com/nette/nette)) and if you're foreigner, you might know it from or [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard/) and [PHPStan](https://github.com/phpstan/phpstan). Event suggested as [`composer.neon`](https://github.com/composer/composer/issues/3228).
    <br><br>
    And `*.yaml` is similar format used almost everywhere else.
    <br><br>
    **You spot the suffix is different, but what about syntax differences? And which one is better?**
tweet: "New post on my blog: Neon vs. Yaml and How to Migrate Between Them"
_tweet_image: "..."
---

None of them is perfect, they both have strong parts and weak parts. But the more I travel to abroad conferences, meetups or repositories, the more I hear **nobody understand differences between them or their advantages to each other**. Since I meet mainly with Symfony and Nette code, I had to investigate them a bit deeper.

<blockquote class="blockquote text-center mt-lg-5 mb-lg-5">
    <a href="https://www.youtube.com/watch?v=3_EtIWmja-4">There are no solutions. There are only trade-offs.</a>
    <footer class="blockquote-footer">
        Thomas Sowell, author of A Conflict of Visions: Ideological Origins of Political Struggles
    </footer>
</blockquote>

Here is a summary of what I found and how to migrate to each other. I'll write about differences and places, where syntax fail me the most.

## How is Syntax Differ?

70 % of syntax is similar:

```yaml
# easy-coding-standard.yml
services:
    Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer: ~
```

But what about?

```yaml
items:
  -
    - { key: value }
```

Of course you can Google documentation and try to understand it. But documentation is  incomplete or for older version than you use.

**The best way to learn it for me is good old *input/output way*** with these 2 online parsers:

- [ne-on.org](https://ne-on.org/)
- [yaml-online-parser.appspot.com](http://yaml-online-parser.appspot.com/)

<br>

### 1. Tabs vs. Spaces

**Neon**

As neon was born as free format, it allows to use both spaces and tabs.

**Yaml**

Only spaces are allowed. Since most of projects have coding standards, I prefer using one format in whole code.

<br>

### 2. Magic List Combination vs. Single Type

```yaml
services:
    - SomeService
    SomeService: ~
```

**Neon**

Could you guess the output? 1 item? Syntax error?

```php
array (1)
    services => array (2)
        0 => "SomeService" (11)
        SomeService => "~"
```

Neon allows to combine indexed arrays and lists.
And do you work with or create with such lists in PHP?

**Yaml**

Parsing would fail there, because Yaml allows only one approach:

```yaml
services:
    - SomeService
    - SomeService
```

with 2 items in array or

```yaml
services:
    SomeService: ~
    SomeService: ~
```

with 1 item as output.

This difference is one of the biggest WTFs, because I had to think about format and possible merge error every time I used lists... or indexed arrays... or arrays. Uff.

<br>

### 3. Content on Multi-lines?

I write posts in Statie, where you can use Yaml to configure per-post variables (@todo linn to post), like perex:

**Neon**

```yaml
perex: '''
    This is long multiline perex,
that takes too much space.
'''
```

Note it can be aligned to left side.

**Yaml**

```yaml
perex: |
    This is long multiline perex,
    that takes too much space.
```

But here it must be indented on every line.

<br>

### 4. Very Complex Syntax

**Neon**

In Neon you can use *entities* and do this:

```yaml
someValue: Column(type=int, nulls=true)
```

Could you guess what it is? Parameters, arguments, service decoration?

```php
array (1)
    someValue => Nette\Neon\Entity
        value => "Column" (6)
        attributes => array (2)
            type => "int" (3)
            nulls => true
```

Personally **I prefer explicit, clear naming** combined with easier scalability:

```yaml
someValue:
    value: "Column"
    attributes:
        type: "int"
        nulls: true
```

**Yaml**

You can do similar shenaniganz with Yaml as well thanks to `Symfony\ExpressionLanguage`

```yaml
# easy-coding-standard.yml
services:
    App\Mailer:
        arguments: ["@=service('App\\\\Mail\\\\MailerConfiguration').getMailerMethod()"]
```

If you want to see real-life example, I [tried it once](https://www.tomasvotruba.cz/blog/2018/03/08/why-is-collector-pattern-so-awesome/#2-use-expression-language). But went quickly back because I could not remember what exactly that means and how it work.

## How is the Ecosystem Support?

This is the most important question when it comes to open-source code.

You can create your own super cool language, that is amrty, context aware and super fast. But what if [1.39 billion people speaks English already](https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers)?

### PHPStorm Support

**Neon**

You can install [Neon Plugin](https://plugins.jetbrains.com/plugin/7060-neon-support), that handles param and class autocomplete very nicely. It's enabled for every `*.neon` file by default.

**Yaml**

Yaml support is included in [Symfony Plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin). It needs to by enabled per project.

There is just one last thing I miss in Symfony Plugin. It already completes services for Symfony 3.2- format:

```yaml
services:
    some_name:
        class: AutocompletedClass
```

But since Symfony 3.3 there is also [short services syntax](https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration#short-syntax-for-service-configuration):

```yaml
services:
    ManuallyTypedService: ~
```

Do you want this feature too? [Upvote this issue](https://github.com/Haehnchen/idea-php-symfony2-plugin/issues/1153) or send PR in Java to plugin.

As for other places, Github [has only Yaml support](https://github.com/phpstan/phpstan/pull/222),

## Who is the Winner?

Which one to pick? It depends, what is **important to you**.
If you use Nette and work in Czech company and Neon is weapon of choice for you, it's ok.

But what if you're **making open source for the whole world**?

<a href="https://xkcd.com/927/">
    <img src="https://imgs.xkcd.com/comics/standards.png">
</a>

### Why are Standards so Important?

I was on a train trip in Hungary and I was thirsty. I go to classic food shop and pick first bottle I see. I wanted still water cause gas hurts while drinking a lot. And I'm drunk when it comes to water.

<img src="/assets/images/posts/2018/neon-yaml/bottle-mixed.jpg" class="img-thumbnail">

In every single country I've been to so far, the **dark blue is always still water**. But not in Hungary!

 As [Chris says](http://chrisinbrnocr.blogspot.cz/2015/08/european-heat-wave.html): "In Hungary the color code is reversed where blue means sparkling and red means flat." And there is even question [on Tripadvisor](https://www.tripadvisor.com.au/ShowTopic-g274887-i263-k7231074-Water_bottle_cap_colour_codes-Budapest_Central_Hungary.html) on this topic.

For all the reasons above (thirsty-human-friendly-bottle-colors included), after looking at problem from various points of view and discussing with my Github and PHP friends, I came to conclusion that Yaml is better for me.

## How to Migrate from Neon to Yaml?

But EasyCodingStandard was running on `*.neon` that was loaded by my few classes to Symfony Kernel, so how to migrate to `*.yml`?

**Imports**

```diff
-includes:
+imports:
-    - packages/EasyCodingStandard/config/psr2.neon
+    - { resource: 'packages/EasyCodingStandard/config/psr2.yml' }

-    - common/array.neon
-    - common/control-structures.neon
-    - common/docblock.neon
+    - { resource: 'common/*.yml' }
```

**Lists**

```diff
 checkers:
     # class should be Abstact or Final
-    - SlamCsFixer\FinalInternalClassFixer
+    SlamCsFixer\FinalInternalClassFixer: ~
     ArrayFixer: ~
```

**Quoting parameters**

```diff
 parameters:
     skip:
         SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
-            - *packages/CodingStandard/src/Sniffs/*/*Sniff.php
+            - '*packages/CodingStandard/src/Sniffs/*/*Sniff.php'
```

To see what code exactly had to change:

- see [pull-request on Symplify\EasyCodingStandard](https://github.com/Symplify/Symplify/pull/651)
- or [Rector with `Extension` => `services` migration](https://github.com/rectorphp/rector/pull/335)
