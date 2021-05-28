---
id: 141
title: "7 Tips to Write Exceptions Everyone Will Love"
perex: |
    `InvalidArgumentException`, `FileNotFoundException`, `InternalException`.
    <br><br>
    Have you ever had that feeling, that **you've seen that exception before and you know what it means and how to solve?** What if that would be clear even for those who see it for the first time? It would save yours and their time.
    <br><br>
    Exceptions are not just error state. **Exceptions are the new documentation**.
tweet: "New Post on my Blog: 7 Tips to Write #Exceptions Everyone Will Love    #tracy #donotmakemethink #php #exceptions"

updated_since: "November 2020"
updated_message: |
    Updated content with simpler examples.
    Switched YAML to PHP configuration, as current standard.
---

I wrote a [50-page thesis about polyphasic sleep](/blog/2018/02/12/sleep-shorter-to-get-62-percent-smarter/). My opponent told me, that there is a missing part about uncontrolled intervening values. The part in pages 34-36 he probably skipped. Today we have too much going on **we have to scan**. Anything longer than 140 chars is exhausting. Moreover for us programmers, who dance among tsunami of information coming every hour as they code and investigate code of others.

### Do you Find this "Circle of Code" Familiar?

1. you open the application
2. you code and code, life is great!
3. suddenly, it's broken by `InvalidStateException` exception ([if we're lucky](https://github.com/thecodingmachine/safe#the-problem))
4. you open exception in IDE to find out more... nothing
5. you open the documentation to find out more... nothing
6. you Google and StackOverflow to find out more... nothing
7. you close the application frustrated, have a ☕ or social joint to restore your will to overcome shit code

**What if you could stay between 1, 2 and 3 much more often?**

<br>

When people used EasyCodingStandard for their first time, they experienced many WTFs. After 30&nbsp;minutes they gave up saying "coding standards are hard". When I asked them to show me how they used it, **it took me 3 minutes to solve**. Why? Because I made it? Well maybe. But also because **exceptions were so lousy, that nobody knew how to solve them** and that's wrong. Shame on me.

## 1. Make Exception Names for Humans

Not machines but people read exceptions. Well, machines read it to but they just log it - humans have to make code work again.

<blockquote class="blockquote">
    <em>InvalidStateException</em>
</blockquote>

WTF? Could you be more clear?

<blockquote class="blockquote">
     <em>ConfigurationFileNotFoundException</em>
</blockquote>

A-ha, I create `ecs.php` and it works!

**+10 % happier programmer**

Do you need help with this? There is [Sniff](https://github.com/symplify/coding-standard#use-explicit-and-informative-exception-names-over-generic-ones) that makes sure no exception is generic.

## 2. Use " around" Statements

<blockquote class="blockquote">
     <em>Filter class  VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd was not found</em>
</blockquote>

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('filters', [
        ' VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd'
    ]);
};
```

The class **exists** and it **is** autoloaded:

```php
require_once __DIR__ . '/vendor/autolaod.php'

var_dump(class_exists(
    VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd::class
));
// "true"
```

So what is wrong?

<br>

*5 minutes later...*

```diff
 <?php

 declare(strict_types=1);

 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

 return function (ContainerConfigurator $containerConfigurator): void {
     $parameters = $containerConfigurator->parameters();
     $parameters->set('filters', [
-        ' VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd'
+        'VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd'
     ]);
 };
```

Ah, there was a space, a single small space!

You probably noticed it, because there are 3 lines of code and they get all your attention. In reality, there are 80 lines of code, 5 files opened in your IDE/brain and your colleague is asking you for wise advice, so your chances to spot this are much lower.

**How to prevent this from happening ever again to anyone in the world?**

<blockquote class="blockquote">
     <em>Filter class  VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd was not found</em>
</blockquote>
*Filter class " VeryLongNamespace\InNestedNamespace\WithMissingClassInTheEnd" was not found*

**Use quotes around every argument:**

```php
throw new FilterClassNotFoundException(sprintf(
    'Filter class "%s" was not found',
    $filterClass
));
```

**+20 % happier programmer**

## 3. What Exactly is Wrong?

<blockquote class="blockquote">
     <em>main parameter is invalid</em>
</blockquote>

```php
throw new InvalidParameterException(sprintf(
    '%s parameter is invalid.',
    $parameterName
));
```

What parameter?

<blockquote class="blockquote">
     <em>"main" parameter is invalid</em>
</blockquote>

```diff
 throw new InvalidParameterException(sprintf(
-    '%s parameter is invalid.',
+    '"%s" parameter is invalid.',
     $parameterName
));
```

Aha! Where do I find it? In the "parameters" section?

<blockquote class="blockquote">
     <em>Parameter in "parameters > page_name > main" is invalid</em>
</blockquote>

```php
throw new InvalidParameterException(sprintf(
    'Parameter in "parameters > page_name > %s" is invalid.',
    $parameterName
));
```

Aha, now I know where to find it, thanks!

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('page_name', [
        'main' => []
    ]);
};
```

**+20 % happier programmer**

## 4. What is The Wrong Value?

<blockquote class="blockquote">
     <em>Parameter in "parameters > page_name > main" is invalid</em>
</blockquote>

We already know *where* it is, but **what value it actually has**?

<blockquote class="blockquote">
     <em>Parameter value "false" in "parameters > page_name > main" is invalid</em>
</blockquote>

I see, so "main" parameter can't have `false` value. What it **can have** then?

<blockquote class="blockquote">
     <em>Parameter value "false" in "parameters > page_name > main" is invalid. It must be a string</em>
</blockquote>

```php
if (is_array($value)) {
    $value = 'array';
} elseif (is_bool($value)) {
    $value = ($value === true) ? 'true' : 'false';
}

throw new InvalidParameterException(sprintf(
    'Parameter value "%s" in "parameters > page_name > %s" is invalid. It must be a string.',
    $value,
    $parameterName
));
```

**+15 % happier programmer**

Tip: You can use [Tracy](https://github.com/nette/tracy) to delegate the value dumping.

## 5. What File Exactly is Broken?

<blockquote class="blockquote">
     <em>Invalid file</em>
</blockquote>

In EasyCodingStandard, PHP_CodeSniffer, PHP CS Fixer, Rector or PHPStan there is always work with files. Is there some error with the file? Show it!

<blockquote class="blockquote">
     <em>File /var/www/tomasvotruba.com/packages/src/TweetPublisher.php not found</em>
</blockquote>

Oh, sorry:

<blockquote class="blockquote">
     <em>File "/var/www/tomasvotruba.com/packages/src/TweetPublisher.php" not found</em>
</blockquote>

**Absolute paths can be really long** in Docker, CI or in the non-basic install. We don't need irrelevant information - every character counts!

How can we make it a bit more familiar to the user? Show the **relative path**:

<blockquote class="blockquote">
     <em>File "packages/src/TweetPublisher.php" not found</em>
</blockquote>

**How to do this nice and lazy in PHP?**

```php
// "$filePath" can be absolute or relative; we don't care, it only must exists
$fileInfo = new SplFileInfo($filePath);

// remove absolute path start to cwd (current working directory)
$relativePath = substr($fileInfo->getRealPath(), strlen(getcwd()) + 1);

throw new FileProcessingException(sprintf(
    'File "%s" not found',
    $relativePath
));
```

**+20 % happier programmer**

## 6. What Options do I have?

```bash
vendor/bin/finder show laravel
```

<blockquote class="blockquote">
     <em>"laravel" was not found</em>
</blockquote>

- Is incorrectly loaded?
- Should I register it somehow?
- Where do I find these levels so I know what are available?

[Don't make the programmer think](https://www.amazon.com/Dont-Make-Think-Revisited-Usability-ebook-dp-B00HJUBRPG/dp/B00HJUBRPG)!

<blockquote class="blockquote">
     <em>Level "laravel" was not found. Pick one of: "symfony", "nette", "zend"</em>
</blockquote>

That's better!

If there is the limited or reasonable amount of options, don't be shy. Show them!

```php
$allOptions = $this->findAllLevelsInDirectory($configDirectory);

throw new OptionNotFoundException(sprintf(
    'Option "%s" was not found. Pick one of: "%s"',
    $optionName,
    implode('", "', $allOptions)
));
```

**+40 % happier programmer**

## 7. Link what You can't Fit 140 Chars

Sometimes you might find yourself writing a poem instead of an exception:

```php
throw new ConfigurationFileNotFound(
    'Class not found. Configure autoload, you can use either `parameters > autoload_files`' .
    'or `parameters > autoload_directories`. Be careful to use paths relative to the file you are using.'
);
```

Who would read that? Except for the author of course.

To make it shorted and readable, we end up with a lousy statement like:

```php
throw new ConfigurationFileNotFound('Class not found. Configure autoload first.');
```

10 Tweets = 1 post, so just link it!

```php
throw new ConfigurationFileNotFound(
    'Class not found. Configure autoload: https://github.com/rectorphp/rector/blob/master/README.md'
);
```

A bit more perfect? **Use headline anchor**:

```php
throw new ConfigurationFileNotFound(
    'Class not found. Configure autoload: https://github.com/rectorphp/rector/blob/master/README.md#extra-autoloading'
);
```

Here is one pitfall - end of lines and line breaks in CLI. You might end up with this error message:

```bash
https://github.com/rectorphp/rector/blob/
master/README.md#extra-autoloading
```

Where only `https://github.com/rectorphp/rector/blob/` is a link. Invalid link.

How to save this?

```php
throw new ConfigurationFileNotFound(
    'Class not found. Configure autoload:' .
    PHP_EOL .
    'https://github.com/rectorphp/rector/blob/master/README.md#extra-autoloading'
);
```

Kaboom!

**+30 % happier programmer**

<br>

And in these 7 steps, you just made any programmer using your code 125 % happier!

**What are your the most favorite exceptions?**

<br>

Happy throwing!

