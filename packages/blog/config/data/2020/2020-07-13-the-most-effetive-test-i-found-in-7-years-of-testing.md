---
id: 268
title: "The most Effective Test I found in 7 years of Testing"
perex: |
    Do you test your projects with automated tests? If not, would you like to start?
    Do you work with application, integration, functional, unit, and Selenium layers and drive you crazy? Do you spend more time writing tests than the actual code behind them?
    <br><br>
    I want my **tests to be simple, effective, and fun to write and maintain**. Today, we look at one approach used by [PHP itself](https://github.com/php/php-src/), `nikic/php-parser`. It's so good I'm surprised not everyone is using it.

tweet: "New Post on #php 🐘 blog: The most Effective Test I found in 7 years of Testing"
---

## The Bigger the Scope, the Greater the Teacher

You can spend a year building house of your dreams. You already live in a flat, so you have a place to sleep. **There is no pressure**. You go there every weekend, add a window here and there, add doors, prepare holes in walls for electric heating cables... good 2-4 years.

How would the situation change if you'd build houses as a developer (the building one)? Your job is to build 100 flats per year. You can't fool around with the color of the walls inside each room. **You have to be effective.**

<br>

The first case of building one house for a year - you only work on 1 PHP project at once. It's only **natural to try-out all the testing layers** you can Google. There are a couple of tests that test the product checkout process, a couple of integration tests to check the component is rendered correctly, [mocking](/blog/2018/06/11/how-to-turn-mocks-from-nightmare-to-solid-kiss-tests/) to "decouple" one part from another.

Every month I work with ~5 private projects, and I maintain [35 open-sources packages](https://packagist.org/profile/?page=3). I used to have very complicated tests for all possible application parts, but that turned out to take more time to maintain to develop, and it **slowed down my productivity brutally the more test I had**.

<br>

## How do PHP-core test case Look Like?

Let's look at the PHP test [with name 001](https://github.com/php/php-src/blob/master/tests/basic/001.phpt).
Give it 60 seconds:

```bash
--TEST--
Trivial "Hello World" test
--FILE--
<?php echo "Hello World"?>
--EXPECT--
Hello World
```

Do you need a PHP-core developer to explain the whole testing process? You don't; you get it.

When we call:

```php
<?php echo "Hello World"?>
```

We get the output:

```bash
Hello World
```

The 1st line is just a description, useful for a more complicated case.

## Testing at its Best

- you **don't maintainer to explain it**
- you **don't have to read a book about testing** to be able to contribute
- you can edit it, and you can extend it
- you can learn with growing complexity by yourself

It's like a smartphone or door handle in testing.

This kind of testing gives you confidence, and that's by far the most important feeling that [builds senior code bases](/blog/2020/03/02/we-do-not-need-senior-developers-we-need-senior-code-bases/).

## Domain Driven Testing

You're probably thinking, "but how do apply this to my unique startup that does not compare strings"? Of course, there is a place for the complex test that checks your checkout process work. The goal **is not to narrow all your tests to 1 size** to fit em all.

The goal is to find what startup is different from other projects. Is your specialty to build e-commerce websites, or is it a recommendation of the next best product? Is it an instant delivery of warm food or a reliable video conference for massive numbers of users?

## Find Your Domain

**Find your domain**, because in this domain will be placed 80 % of your tests. If you pick the right domain and make these tests simple, effective, and fun to write and maintain, your developers will enjoy writing them, and your code will naturally grow.

<br>

Let's look at the projects you know:

- PHP
    - **main domain**: running PHP input code with correct output to the user
    - side domain: validation of input, syntax check, informative errors
- php-parser
    - **main domain**: parsing PHP input code to desired node objects
    - side domain: printing nodes back, comments handling
- Rector
    - **main domain**: refactoring input code to the desired change
    - side domain: working with docblocks and types, format-preserving


## Code Time

Enough theory, let's do the practice.

We have a fixture file with your domain, `/fixture/first_try.php`:

```bash
input
-----
expected output
```

Then we need to run Test Case:

```php
<?php

use PHPUnit\Framework\TestCase;

final class FirstTryTest extends TestCase
{
    public function test(): void
    {
        $fixtureContent = file_get_contents(__DIR__ . '/fixture/first_try.php');
        [$input, $expectedOutput] = explode("\n-----\n", $fixtureContent);

        // test your main domain service
        $output = $this->processInputInYourDomain($input);

        $this->assertSame($expectedOutput, $output);
    }
}
```

And that's it :) See [3v4l.org code sample](https://3v4l.org/sEudR).

<br>

## Few Tips before Start

- it's all at one place - no jumping file jumping and looking for the right file

```diff
-/tests/fixture/before/input_string.php
-/tests/fixture/after/some_string_print.php
+/tests/fixture/change_string.php
```

- the file name is the description
- **it scales** - you can build a test that combines multiple problems at once, and still get the same input/output format
- you can also **combine formats** in one fixture, e.g., for [migration of Symfony configs from YAML to PHP](https://github.com/migrify/migrify/blob/master/packages/config-transformer/packages/format-switcher/tests/Converter/ConfigFormatConverter/FixtureYamlToPhp/normal/some.yaml).

<br>

## What If Output Changes?

"But what happens when we add a new property to the output? Do we have to change all the files manually? That's crazy."

It would be crazy. I tried to update 60 files in php-parser when I only added typed properties... oh, that was too much work. At file 50, I figured out there is an automated way exactly my case. We look at how we can **turn these tests into snapshot tests that update themselves** in the next post.

<br>

PS.: Do you want to automate part with loading and splitting fixture? Checkout [symplify/easy-testing](https://github.com/symplify/easy-testing) package.

<br>

Happy coding!
