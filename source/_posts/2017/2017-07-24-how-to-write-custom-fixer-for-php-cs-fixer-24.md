---
id: 47
title: "How to Write Custom Fixer for PHP CS Fixer 2.4"
perex: |
    You already know <a href="/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/">how coding standard tools work with tokens and how to write a Sniff</a>.
     <br><br>
     Today we'll explore a bit younger tool - <a href="https://github.com/FriendsOfPHP/PHP-CS-Fixer">PHP CS Fixer</a> and we get <strong>from only finding the smelly spot to fixing it</strong>.
related_items: [48, 46, 37]
tweet: "How to Write Custom Fixer for #phpcsfixer"
tweet_image: "/assets/images/posts/2017/php-cs-fixer-intro/php-cs-fixer-require.png"

updated: true
updated_since: "April 2018"
updated_message: |
    Updated with <a href="https://github.com/Symplify/Symplify/blob/master/CHANGELOG.md#v400---2018-04-02">ECS 4.0</a>, Neon to Yaml migration and `checkers` to `services` migration.
---

**Are you new to PHP Coding Standard Tools**? You can read intro [How PHP Coding Standard Tools Actually Work](/blog/2017/07/31/how-php-coding-standard-tools-actually-work/) to grasp the idea behind them. Or [just go on](https://www.youtube.com/watch?v=t99KH0TR-J4&feature=youtu.be&t=16) if you're ready to start...

<br>

When a coding standard tool finds over 1000 violations in our code is nice to know, but it doesn't save us any time and energy we need for <a href="http://calnewport.com/books/deep-work/">a deep work</a>.

### Find & Fix It

That main difference of PHP CS Fixer to PHP_CodeSniffer is that **every Fixer has to fix issues it finds**. That's why there is no `LineLengthFixer`, because fixing line length is difficult to automate.

Personally I like PHP CS Fixer a bit more, **because of more friendlier API, active community and openness to 3rd party packages**:

<img src="/assets/images/posts/2017/php-cs-fixer-intro/php-cs-fixer-require.png" class="img-thumbnail">
<p>
    <em><code>composer.json</code> from PHP CS Fixer</em>
</p>

<br>

<img src="/assets/images/posts/2017/php-cs-fixer-intro/code-sniffer-require.png" class="img-thumbnail">
<p>
    <em><code>composer.json</code> from PHP_CodeSniffer</em>
</p>

<br>

Apart that, they are similar: they share [tokens](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/#1-token),
[dispatcher](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/#2-dispatcher)
 and [subscribers](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/#2-dispatcher).


Yet still, working with tokens is counter intuitive to way we work with the code (class, method, property...), but I'll write about that later.

Now we jump to writing the Fixer class.


## 7 Steps to Make an `ExceptionNameFixer`

"An exception class should have "Exception" suffix."

In last post, we made
[ExceptionNameSniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/#let-s-make-code-exceptionnamesniff-code-together), that will:

- find an "extends" token
- check if parent class is an Exception
- find a current class name
- check if ends "Exception"

Today we'll add one more step:

- **fix the name to end with "Exception"**


### 1. Implement an Interface

Create a fixer class and implement a `PhpCsFixer\FixerDefinition\FixerDefinitionInterface` interface.

It covers 7 required methods, but most of them are easy one-liners:

```php
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ExceptionNameFixer implements DefinedFixerInterface
{
    # first 5 methods are rutine and descriptive

    public function getName(): string
    {
    }

    public function getDefinition(): FixerDefinitionInterface
    {
    }

    public function isRisky(): bool
    {
    }

    public function supports(SplFileInfo $file): bool
    {
    }

    public function getPriority(): int
    {
    }

    # in last 2 methods, the magic happens :)

    public function isCandidate(Tokens $tokens): bool
    {
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
    }
}
```

### 2. Easypicks First

I start with implementing first 5 methods, to make

```php
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ExceptionNameFixer implements DefinedFixerInterface
{
    public function getName(): string
    {
        return self::class;
    }

    // this methods return the error message
    // and it might include a sample code, that would fix it
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Exception classes should have suffix "Exception".',
            [
                new CodeSample(
                    '<?php
    class SomeClass extends Exception
    {
    }'
                ),
            ]
        );
    }

    // if the fixer changes code behavior in any way, return "true"
    // changing a class name is such case
    public function isRisky(): bool
    {
        return true;
    }

    // in 99.9% this is true, since only *.php are passed
    // you can detect specific names, e.g. "*Repository.php"
    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    // it's used to order all fixers before running them
    // `0` by default, higher value is first
    public function getPriority(): int
    {
        return 0;
    }
}
```

### 3. Subscribe the Fixer

Now we get to more interesting parts. Method `isCandidate(Tokens $tokens): bool` is like a **subscriber**. It gets all tokens of the file. We can check **more than one token and create more strict conditions** thanks to that:

```php
public function isCandidate(Tokens $tokens): bool
{
    return $tokens->isAllTokenKindsFound([T_CLASS, T_EXTENDS, T_STRING]);
}
```

`extends` token without class and its name is useless and not a code we want to match.

### 4. Add "Fix it" part

```php
public function fix(SplFileInfo $file, Tokens $tokens): void
{
}
```

This methods get same tokens as `isCandidate()` and the file info.

**How to build a fixer?**

- First we need to detect, if this is the use case we try to match - **an exception class**. Because `T_EXTENDS` doesn't tell a lot.
- Then we need to check if it meets our rules
- and fix if so.

Let's take it one a by one:

### 5. Detect the Exception Class

*A class that extends another class that has suffix "Exception".*

There is a bit different paradigm compared to PHP_CodeSniffer. We don't get position of the `extends` token, but **all the tokens**. Instead of investigating one token and it's relation to other, **we need to iterate through all tokens and match them with conditions**:

```php
public function fix(SplFileInfo $file, Tokens $tokens): void
{
    foreach ($tokens as $index => $token) {
        // is there extends token?
        if (! $token->isGivenKind(T_EXTENDS)) {
            continue;
        }

        // is this exception class?
        if (! $this->isException($tokens, $index)) {
            continue;
        }

    }
}
```

**How to detect an exception class?**

`Tokens` (like `File` in PHP_CodeSniffer) has helper methods to make our life easier.

**First of them is `getNextMeaningfulToken()`, which skips spaces and comments and seeks for first useful one**. In our case, after `extends` we look for a parent class name.

```php
private function isException(Tokens $tokens, int $index): bool
{
    $parentClassNamePosition = $tokens->getNextMeaningfulToken($index);
    // $tokens support array access - to get a token with some index, call $tokens[25]
    $parentClassNameToken = $tokens[$parentClassNamePosition];
    $parentClassName = $parentClassNameToken->getContent();

    return $this->stringEndsWith($parentClassName, 'Exception');
}

private function stringEndsWith(string $name, string $needle): bool
{
    return substr($name, -strlen($needle)) === $needle;
}
```

Back to iteration! When this passes, we know **we have a class that extends an exception**.

Do you know what we need to do now? You're right, **we have to check its name**. We can use another helper method: `getPrevMeaningfulToken()`.

```php
public function fix(SplFileInfo $file, Tokens $tokens): void
{
    foreach ($tokens as $index => $token) {
        // is there extends token?
        if (! $token->isGivenKind(T_EXTENDS)) {
            continue;
        }

        // is this exception class?
        if (! $this->isException($tokens, $index)) {
            continue;
        }

        // does this class ends with "Exception"?
        $classNamePosition = (int) $tokens->getPrevMeaningfulToken($index);
        // get the token
        $classNameToken = $tokens[$classNamePosition];
        // check its content
        if ($this->stringEndsWith($classNameToken->getContent(), 'Exception')) {
            continue;
        }

    }
}
```

### 6. Fixing the Error

Fixing is right to the point. To change a name, replace old name (`T_STRING` `Token`) with new `Token` object with different value:

```php
// Token(token type, value)
$tokens[$classNamePosition] = new Token([T_STRING, $classNameToken->getContent() . 'Exception']);
```

Is that it? Yea, that's it :)

### 7. Put Together The Final Fixer

```php
namespace App\CodingStandard\Fixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ExceptionNameFixer implements DefinedFixerInterface
{
    public function getName(): string
    {
        return self::class;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Exception classes should have suffix "Exception".',
            [
                new CodeSample(
                    '<?php
    class SomeClass extends Exception
    {
    }'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_EXTENDS, T_STRING]);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_EXTENDS)) {
                continue;
            }

            if (! $this->isException($tokens, $index)) {
                continue;
            }

            $classNamePosition = (int) $tokens->getPrevMeaningfulToken($index);
            $classNameToken = $tokens[$classNamePosition];
            if ($this->stringEndsWith($classNameToken->getContent(), 'Exception')) {
                continue;
            }

            $tokens[$classNamePosition] = new Token([T_STRING, $$classNameToken->getContent() . 'Exception']);
        }
    }

    private function isException(Tokens $tokens, int $index): bool
    {
        $parentClassNamePosition = $tokens->getNextMeaningfulToken($index);
        $parentClassNameToken = $tokens[$parentClassNamePosition];
        $parentClassName = $this->getParentClassName($tokens, $index);

        return $this->stringEndsWith($parentClassName, 'Exception');
    }

    private function stringEndsWith(string $name, string $needle): bool
    {
        return (substr($name, -strlen($needle)) === $needle);
    }
}
```

## How to run it?

### The PHP CS Fixer way

Create `.php_cs` config and register fixer with `registerCustomFixers()` method, like here in [`shopsys/coding-standard`](https://github.com/shopsys/coding-standards/blob/5f7c5e61f3a5ddd279887ac51a2bcb5f6bc81d78/build/phpcs-fixer.php_cs#L54).

```php
return PhpCsFixer\Config::create()
    ->registerCustomFixers([
        new App\CodingStandard\Fixer\ExceptionNameFixer,
    ]);
```

And run:

```bash
vendor/bin/php-cs-fixer fix src --config=.php_cs --dry-run
```

### The [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) way

Put the class to `easy-coding-standard.yml`:

```yaml
services:
    App\CodingStandard\Fixer\ExceptionNameFixer: ~
```

And run:

```bash
vendor/bin/ecs check src
```

That was your first fixer.

Happy fixing!


And if you want **more detailed tutorial**, there is one in [official cookbook](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/COOKBOOK-FIXERS.md).
