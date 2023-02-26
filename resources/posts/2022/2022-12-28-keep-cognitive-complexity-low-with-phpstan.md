---
id: 370
title: "Keep Cognitive Complexity Low&nbsp;with&nbsp;PHPStan"
perex: |
    It seems specialized PHPStan packages are fun and easy to use. I made one to keep code readable and avoid piling up legacy code nobody wants to touch.


    Why is cognitive complexity such a powerful predictor of readable or unreadable code?
---

What is cognitive complexity? It's the amount of information we have to hold in our heads simultaneously to understand the code. The more indents, continue, break, nested foreach, and if/else branches, the harder is code to read.

Let's jump right to an example. We are in the middle of the holidays and want to share our Christmas spirit with the world. Let's write a simple script to do that:

```php
echo 'I got ' . get_words_from_number(100) . ' gifts for Christmas!';
```

<br>

We're interested in the body of `get_words_from_number()`. We measure the complexity line by line:

```php
function get_words_from_number(int $number): string
{
    $amountInWords = '';

    if ($number === 1) {            // + 1
        $amountInWords = 'one';
    } elseif ($number === 2) {      // + 1
        $amountInWords = 'couple';
    } elseif ($number === 3) {      // + 1
        $amountInWords = 'a few';
    } else {                        // + 1
        $amountInWords = 'a lot';
    }

    return $amountInWords;
}
```

This code has **cognitive complexity of 4**. It's a pretty bumpy ride to understand it, right?

<br>

How can we **make it lower**, so it's easier to understand?

We should use the following:

* early return,
* early continue,
* extract a method,
* for more complex code, extract a service,
* or, in this case, a `match` structure:

<br>

```php
function get_words_from_number(int $number): string
{
    return match ($number) { // +1
        1 => 'one',
        2 => 'a couple',
        3 => 'a few',
        default => 'lots',
    };
}
```

## 3 Steps to Keep Cognitive Complexity Low with PHPStan

1. Install the [TomasVotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity) package

```bash
composer require tomasvotruba/cognitive-complexity --dev
```

<br>

*Not a phpstan/extension-installer user yet? [Add it now](https://github.com/phpstan/extension-installer) to enjoy the easy install without copying config paths.*

<br>

2. Setup PHPStan configuration:

```yaml
# phpstan.neon
parameters:
    cognitive_complexity:
        class: 50
        function: 8
```

<br>

3. Run PHPStan:

```bash
vendor/bin/phpstan
```

<br>

Does PHPStan report 1000 cases? No worries, no project ever passed it on the first run. Keep yourself calm, **make the value higher** and re-run:

```diff
 # phpstan.neon
 parameters:
     cognitive_complexity:
-        class: 50
+        class: 70
-        function: 8
+        function: 15
```

Are there just 5 cases? Fix those to make CI pass again!

<br>

## 2 Areas where Cognitive Complexity Rocks

This package turned out to be very helpful in 2 phases:

At first, it helps to lower the hard spots. In sum, this turn the codebase [into a senior codebase](https://tomasvotruba.com/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/), it's easier to understand for fresh developer on the project and grow further.

<br>

Second, it gives quick feedback on the code review. If you get the following error:

```bash
"the 'simpleFetch()` method cognitive complexity is over 30, make it simpler"
```

You **know you can be more creative** and improve it for better readability.

<br>

Happy coding!
