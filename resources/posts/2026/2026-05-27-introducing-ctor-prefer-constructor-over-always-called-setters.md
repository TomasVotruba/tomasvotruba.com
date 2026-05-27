---
id: 436
title: "Introducing CTOR: Prefer Constructor over Always-Called Setters"
perex: |
    We've all seen it: `new Human()` followed by three `setName()`, `setAge()`, `setEmail()` calls that are always there. Not optional - just pretending. These are constructor arguments in a fluent disguise, and they leave your objects half-valid every time someone forgets one.

    So I made a small PHPStan extension to find them.
---

When you build a house, you don't pour the foundation first and then hope someone remembers to add the load-bearing walls a week later. You build the structure that *has to be there* at the start.

Yet in PHP, we sometimes do exactly the opposite:

```php
$human = new Human();
$human->setName('Tomas');
$human->setCountry('Czech Republic');
```

Three lines, and only the first one is enforced by the language. The other two? A polite suggestion. Forget one, and `$human` quietly walks around half-built until something explodes deep inside a service three layers down.

I've been bumping into this pattern on legacy codebases for years. Most of the time, those setters are not optional - they're **always called right after `new`**. They pretend to be flexible, but in practice they are required dependencies wearing a fluent disguise.

So I made a small PHPStan extension to find them.


## What does the package do?

Ctor adds a PHPStan rule that looks for this exact shape:

```php
$human = new Human();
$human->setName('Tomas');
$human->setCountry('Czech Republic');
```

...and suggests turning it into this:

```php
$human = new Human('Tomas', 'Czech Republic');
```

That's it. No magic, no codemod, no auto-fix. Just a clear nudge from PHPStan: "hey, these setters look suspiciously mandatory - have you considered the constructor?"


## Why?

Chained setters after `new` are often **implicit required dependencies** in disguise.

- The object is never used between `new` and the last setter call - it's not really "configurable", it's "incomplete"
- Forget a single setter, and the object goes into the world in a half-valid state
- You can't `readonly` the properties, because they have to be writable from outside
- Tests have to repeat the same setter sequence in every fixture
- IDE and PHPStan can't tell you *which* setters are required, because the constructor signature says "I need nothing"

Move it all to the constructor and every one of these problems disappears. The object becomes **valid from the moment it exists** - which is the whole point of having a constructor in the first place.

I wrote about this idea years ago in [How to Hydrate Arrays to Objects via Constructor](https://tomasvotruba.com/blog/2020/04/20/how-to-hydrate-arrays-to-objects-via-constructor), but back then I had to grep for these patterns by hand. Now PHPStan does it for me.


## 2 steps to install

1. Require it via composer:

```bash
composer require tomasvotruba/ctor --dev
```

2. If you use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer), you're done - the rule is loaded automatically.

Now run PHPStan and see how many "constructors-in-hiding" your codebase has:

```bash
vendor/bin/phpstan
```

<br>

The repository is here: [TomasVotruba/ctor](https://github.com/TomasVotruba/ctor)

If you spot a false positive, a missed case, or a related smell you'd like the rule to catch - open an issue, I'd love to hear it.


Happy coding!
