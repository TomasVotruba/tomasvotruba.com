---
id: 250
title: "How to Upgrade to Symplify 8 - From Sniffs to PHPStan Rules"
perex: |
    Since Symplify 7.3, you might notice a few deprecation notices in your coding standards. As Symplify 8 release is [synced with Symfony cycle](/blog/2017/10/30/what-can-you-learn-from-menstruation-and-symfony-releases/), both will be released at the end of May.
    <br>
    <br>
    What to do with these deprecations? Why were these sniffs dropped? How to handle upgrades in 1 hour?
tweet: "New Post on #php 🐘 blog: How to Upgrade to #symplify 8 - From Sniffs to #phpstan Rules"
---

When you run [ECS](https://github.com/symplify/easycodingstandard) with version 7.3+:

```bash
vendor/bin/ecs check
```

You might see such notices right before your code gets checked:

```bash
PHP Notice:  Sniff "..." is deprecated. Use "..." instead
```

## Why were These Sniffs Dropped?

Symplify 7 used lots of sniffs based on coding standard [tokens](https://www.php.net/manual/en/function.token-get-all.php). In time they were made, they were as good. [Tokens are best at spaces, abstract syntax tree is best at logical code structure](/blog/2018/10/25/why-ast-fixes-your-coding-standard-better-than-tokens/#shifting-the-scope).

E.g. `TraitNameSniff` check name of traits and makes sure the name ends with "Trait":

```php
<?php

// hey, this should be "ProductTrait"
trait Product
{
}
```

Do you see any spaces or token positions? No, it's just:

- find a trait
- check its name

So writing this rule in tokens is the wrong tool chosen. Why? With tokens, you need to make sure:

- trait token is found
- there are no spaces after trait token
- detect the name
- for other rules, resolve its fully qualified name based on the namespace and use imports (real hell with tokens)

But **why waste time on re-inventing the wheel**, when we can use a tool that handles tedious work for us?
It's better to use *abstract syntax tree* technology, in this case [PHPStan](https://phpstan.org/).

<br>

**That's why** all the "AST" sniffs were migrated to PHPStan rules.

## What to do With These Deprecations?

So what does it mean? Remove all the rules from `ecs.yaml` and let go?

No, **all you need to do is switch to PHPStan rules**. It's better working and more reliable since it works with context and not token positions. So at first, you might discover a few new reported errors here and there.


## How to Handle Upgrade in 1 hour?

There are 14 deprecated sniffs in total. Don't worry, while it might seem like a considerable number, the replacement is a matter of an hour.

Have you used a full Symplify set?

```yaml
# ecs.yaml
parameters:
    sets:
        - symplify
```

Then you won't see any deprecations, because rules were removed from the config for you. Still, I recommend adding these rules to `phpstan.neon`.

## 1. Abstract, Trait and Interface Naming Sniffs

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff: null
-    Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff: null
-    Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff: null
```

↓

```bash
composer require --dev slam/phpstan-extensions
```

```yaml
# phpstan.neon
rules:
    - SlamPhpStan\ClassNotationRule
```

## 2. Cognitive Complexity

The [only ~~sniff~~ rule your coding standard should have](/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)... just got better.

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff:
-        maxCognitiveComplexity: 10 # default: 8
-    Symplify\CodingStandard\Sniffs\CleanCode\ClassCognitiveComplexitySniff:
-        maxClassCognitiveComplexity: 60 # default: 50
```

↓

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/packages/cognitive-complexity/config/cognitive-complexity-rules.neon

# if you use default value, you can skip this section
parameters:
    symplify:
        max_cognitive_complexity: 10
        max_class_cognitive_complexity: 60
```

## 3. Make sure Classes used in @param, @var and @return Exists

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff: null
```

Easy, drop it. PHPStan level 0 handles this.

## 4. Forbidden Static → Explicit Static

Static functions [are best way to slowly create technical dept](/blog/2019/04/01/removing-static-there-and-back-again/). But sometimes it's tough to code without them.

So instead of forbidding them:

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff: null
```

↓

Just be honest about them:

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule
```

The same way you see a "trait" in a file name and know it's a trait,
this rule makes sure that classes with static methods have "static" in its name.

I've been using it for 4 days, and oh, what a shame I feel when I want to use a "Static" "service". It **makes me think twice both about the design and consequences**.

## 5. Forbidden Parent Class

Even though it's repeated over, again and again, that composition beats inheritance, the PHP code I see is full of it.

Sometimes the only way to [promote so-far-the-best practise](/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/), is to enforce it:

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff:
-        forbiddenParentClasses:
-            - Doctrine\ORM\EntityRepository
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ForbiddenParentClassRule

parameters:
    symplify:
        forbidden_parent_classes:
            - 'Doctrine\ORM\EntityRepository'
```

## 6. Use Custom Exceptions over Basic Ones

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Architecture\ExplicitExceptionSniff: null
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDefaultExceptionRule
```

## 7. Prefer One Class over Another

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff:
-        oldToPreferredClasses:
-            'DateTime': 'Nette\Utils\DateTime'
```

↓

```yaml
# phpstan.neon
parameters:
    symplify:
        old_to_preffered_classes:
            DateTime: 'Nette\Utils\DateTime'

rules:
    - Symplify\CodingStandard\Rules\PreferredClassRule
```

## 8. No Duplicated Short Classes

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff: null
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule
```

## 9. Use explicit Return over `&` Reference

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff: null
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoReferenceRule
```

## 10. Don't leave `dump()` Function in the Code


```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff: null
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDebugFuncCallRule
```

## 11. Respect Naming of Your Parents

```diff
-# ecs.yaml
-services:
-    Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff: null
```

↓

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ClassNameRespectsParentSuffixRule

parameters:
    symplify:
        parent_classes:
            - Rector
```

And that's it! You've just migrated all deprecated sniffs to PHPStan rules. Upgrade to Symplify 8 will be a piece of cake for you.

<br>

[In the next post, we'll look on the 2nd half](/blog/2020/05/11/how-to-upgrade-to-symplify-8-from-fixers-to-rector-rules) - **how to migrate fixers into Rector rules**.

<br>

Happy coding!
