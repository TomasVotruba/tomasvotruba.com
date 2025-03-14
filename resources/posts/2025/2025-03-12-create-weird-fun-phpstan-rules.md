---
id: 430
title: "Create Weird Fun PHPStan Rules like Nobody's Watching"
perex: |
    There are 2 ways to use PHPStan. You can use native levels, and official extensions and raise the level from 0 to 8. This is a good start, but it often requires enormous work and brings must-have value.

    There is also a 2nd way: I wanted PHPStan **to be more fun and more tailored to the unique projects I work with**. That's why I made [symplify/phpstan-rules](https://github.com/symplify/phpstan-rules), a package that just crossed 6 200 000 downloads. It is one of the most used PHPStan extensions apart from official ones.

    I put all the fun and practical rules there, and often they prove to be useful to others too.

    But today I want you to move from end-user to **creator**.
---


<blockquote class="blockquote text-center mt-5 mb-5">
    "You teach me, I forget. You show me, I remember.<br>
    You involve me, I understand."
</blockquote>

<br>

Today we write a custom PHPStan rule together. Not for everyone, but only for you and your local project. We will not write tests, we will not make it 100 % reliable, and we will not cover all edge cases. We will just make it bring value, and make it fun and practical.

That's the real beauty of my own local PHPStan rules - they can be KISS &ndash; Simple & Stupid. I don't have to feel ashamed on socials if they seem too vague or for everyone.

<br>

I work on various codebases, raising [type coverage](/blog/how-to-measure-your-type-coverage) one 1 % at a time. It's a lot of manual work or Copilot exchanges I need to verify. In short: repetitive thinking hurts my brain.

<br>

Last week, I noticed a simple pattern in one of the codebases:

```php
public function get($userId): User
{ /* ... */ }

public function run($userId): void
{ /* ... */ }

public function request($userId, array $params): void
{ /* ... */ }
```

<br>

There is a type missing for `$userId`... what if we know it's always `int` or `string`?

I've checked other calls in codebase + database and made an astounding discovery: **the user id is always an `int`**!

<br>

I wondered: what if we make a PHPStan rule that:

* brings value
* takes 10 minutes to write
* is fun to write

## 10-min Experiment

I often have no idea if the PHPStan rule will work or not, so I put 10 10-minute experiment limit on it. If it doesn't work, I just throw it away. If it does, we keep the rule and improve it.

<br>

Let's dive in!

<br>

## 1. Create PHPStan rule class

First, make a directory:

```bash
/utils/phpstan/src
```

There, we create an empty `ParamTypeByNameRule.php` class:

```bash
/utils/phpstan/src/ParamTypeByNameRule.php
```

## 2. Autoload in `composer.json`

```bash
{
    "autoload-dev": {
        "psr-4": {
            "Utils\\PHPStan\\": "utils/phpstan/src",
        }
    }
}
```

Refresh PSR-4 paths:

```bash
composer dump-autoload
```

## 3. The fun part: write the rule

The boring setup is done, let's write the fun part! What should our PHPStan rule do?

* find a parameter
* check if has a type - yes? skip it
* if not, what is the name of the parameter?
* is it `userId`? suggest an `int`

<br>

Easy-peasy! I think it will be the shortest custom rule ever written.

```php
<?php

namespace Utils\PHPStan;

use PHPStan\Rules\Rule;
use PhpParser\Node\Param;

class ParamTypeByNameRule implements Rule
{
    public function getNodeType(): string
    {
        return Param::class;
    }

    /**
     * @param Param $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // we know the type - let's skip it
        if ($node->type !== null) {
            return [];
        }

        // what is the parameter name?
        $parameterName = $node->var->name->toString();
        if ($parameterName !== 'userId') {
            return [];
        }

        return [
            RuleErrorBuilder::message('The $userId param is missing `int` type')
                ->identifier('custom.paramTypeByName')
                ->build();
        ];
    }
}
```

That's all folks!

<br>

## 4. Register rule in `phpstan.neon`

```yaml
rules:
    - Utils\PHPStan\ParamTypeByNameRule
```

<br>

*Protip*: try running **only this rule alone** without any levels:

```yaml
parameters:
    customRulesetUsed: true

    # comment out level
    # level: 8
```

## 5. RunPHPStan

```bash
vendor/bin/phpstan
```

That's it!

<br>

## 6. Improve...

Does it bring value? If yes, improve it. If not, throw it away.

The next step would be to add more param-name&dash; pairs - like `$articleId`, `$groupName` etc.

<br>


This rule was so much fun to write and use, that I've turned it into a generic one. Raising param type coverage is one the most complex type coverages, and this rule turned it into a fun game that saves time and brain power:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">This is one of the most weird, laziest and easiest PHPStan rules I&#39;ve ever written... 😁<br><br>...and the beauty is, it brings instant real value <br>to any codebase with missing type declarations 😎 <a href="https://t.co/4XQTo8ebhV">pic.twitter.com/4XQTo8ebhV</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1899775072438501389?ref_src=twsrc%5Etfw">March 12, 2025</a></blockquote>

<br>

## 7. Your turn!

Now it's your turn to make however weird, stupid, simple, non-sense PHPStan rule you want. Don't forget - nobody's watching and you can be creative beyond reason.

Can you think it? Write it!

It's your project, your rules, your fun, and if it **brings any value, stick with it**.

<br>

Writing custom PHPStan rules is one of the greatest assets when it comes to raising codebase value in time. It stays in the project after you leave and helps others to keep the codebase clean and safe.

<br>

Happy coding!
