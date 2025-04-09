---
id: 431
title: "Upgrading Sensio Security Annotation: The Full Story"
perex: |
    The `@Security` annotation, which originated in the Sensio extra bundle, goes a long way. The official upgrade docs have a few misleading pointers, that force you to use unnecessary verbose language.

    Fortunately, few hidden levels make code much less verbose and more readable.

    This post sums up upgrading the Sensio `@Security` annotation to Symfony `#[IsGranted()]` attribute in one place.
---

*Note: If you already use the native Symfony attributes, scroll down to find syntax tips.*

<br>

## Starting point

We have a Symfony project that uses `sensio/framework-extra-bundle` to define security rules above the controller class or method:

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER')")
 * @Security("is_granted('read') and is_granted('write')")
 */
class SomeController
{
}
```

We want to:

* get rid of the `sensio/framework-extra-bundle` package
* upgrade annotations to PHP 8.0 attributes
* use Symfony core native `#[IsGranted()]` attribute
* use the best syntax possible
* enable Rector and PHPStan works for us now and in the future


## Evolution Timeline

To give you a better context, here is an evolution of the `@Security` annotation across 2 packages:

* `sensio/framework-extra-bundle`
    * 3.0 [introduced](https://github.com/sensiolabs/SensioFrameworkExtraBundle/commit/82182d14d573b5132180f0a355d7ce9b4a81a84e) `@Security` annotation
    * 4.0 [introduced](https://github.com/sensiolabs/SensioFrameworkExtraBundle/commit/cc49d26a3d75f14f0fee731cade925238f7a4199) `@IsGranted` annotation
    * 6.0 brings **PHP 8.0** attributes `#[Security]` and `'#[IsGranted]`
    * 6.2 (Feb 2023) is the last release, then the repository was archived = no more fixes

<br>

* `symfony/security-http`
    * 6.2 (July 2022) [introduces](https://github.com/symfony/symfony/pull/46907/) native `#[IsGranted]` attribute to allow migration from deprecated Sensio package

<br>

**What is the best way to start?** Should we go directly from Sensio annotations to Symfony attributes? Is the syntax the same? But what about the `@Security` annotation? It doesn't have any native Symfony equivalent.

There are many paths to start this migration. We're lazy devs working on a huge project, so we'll pick the smallest step possible.

## 1. Upgrade Sensio from Annotations to Attributes

We'll start with the upgrade of annotations to attribute for one simple reason: **abstract syntax tree (technology used by Rector) **works better with native PHP attributes** than with lousy docblocks. Once we have native PHP 8.0 attributes, we can start using Rector to help us with the rest of the upgrade.

First, we upgrade at least to `sensio/framework-extra-bundle` 6.0. Then we use Rector to handle the upgrade. We update the config:

```php
# rector.php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAttributesSets(sensiolabs: true)
```

And run Rector:

```bash
vendor/bin/rector
```

That's it, now we have native PHP attributes. Yay!

<br>

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Security("has_role('ROLE_USER')")]
#[Security("is_granted('read') and is_granted('write')"]
class SomeController
{
}
```


## 2. Fix the Security Attribute

Ideally, we could switch directly to the `symfony/security-http` package, but there are 2 small problems:

* there is no `#[Security]` attribute in Symfony core
* and there is a bug in *the Sensio* `#[Security]` attribute:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Today, we&#39;re looking into Symfony <a href="https://twitter.com/security?ref_src=twsrc%5Etfw">@Security</a> annotations.<br><br>Framework bundle extra allowed multiple annotations:<br><br>/**<br> * <a href="https://twitter.com/security?ref_src=twsrc%5Etfw">@Security</a>(&#39;...&#39;)<br> * <a href="https://twitter.com/security?ref_src=twsrc%5Etfw">@Security</a>(&#39;...&#39;)<br> * <a href="https://twitter.com/security?ref_src=twsrc%5Etfw">@Security</a>(&#39;...&#39;)<br> */<br>class SomeController {}<br><br>But only single attribute is allowed:<br><br>#[Security(&#39;...&#39;)]<br>class…</p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1909258798360932420?ref_src=twsrc%5Etfw">April 7, 2025</a></blockquote>





Someone forgot to allow the attribute to be used multiple times. That means the code above will throw a native PHP error because we're using two `#[Security]` attributes in the same place.

<br>

The Sensio repository is archived, so we can't fix it there. No worries, we just [patch `/vendor`](https://tomasvotruba.com/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates) file:

```diff
-#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
+#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Security extends ConfigurationAnnotation
```

## 3. From Sensio `#[Security]` to Sensio `#[IsGranted]`

Before we switch to Symfony core, we want to [get into the best position there is](/blog/mountain-climbing).

<br>

There are 3 steps we can achieve that: we change `is_granted()` to `#[IsGranted()]`:

```diff
 use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
+use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

-#[Security("is_granted('listen')"]
+#[IsGranted('listen')]
 class SomeController
 {
 }
```

Fewer strings, more native code, better code resilience. Now we can instantly see, that "listen" is our custom value.

<br>

## 4. Hidden level: has_role ~= is_granted

How about the `has_role`? Do we have to keep this string or can we improve it somehow?

Actually, the `is_granted()` deprecated and replaced `has_role()` in [Symfony 4.2](https://github.com/symfony/symfony/pull/27305/).

We can replace the role check directly with the `#[IsGranted()]` attribute:

```diff
-use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
+use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

-#[Security("has_role('ROLE_USER')"]
+#[IsGranted('ROLE_USER')]
 class SomeController
 {
 }
```

Nice and clean!

## 5. Hidden level 2: and == multiple items

When I was debugging Sensio to fix the attribute, I discovered the attributes stacked on each other. It means **all of the attributes must pass**, or the access will be denied.

Saying that we can split "and" expressions into standalone attributes:

```diff
-use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
+use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

-#[Security("is_granted('read') and is_granted('write')"]
+#[IsGranted('read')]
+#[IsGranted('write')]
 class SomeController
 {
 }
```

Voilá!

<br>

You can do changes manually, or be lazy like me and let Rector handle steps 3, 4, and 5 for you:

```php
# rector.php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPreparedSets(symfonyCodeQuality: true)
```

## 6. Permission is an Enum!

Now we can build on the steps we've made before. We got from magic verbose definitions in docblock, to neat and tight single-word attributes.

The "read" is not some technical keyword from PHP. It's **a made-up string we use here and most likely somewhere else**. It's an enum we use over and over.

<br>

So let's extract it:

```php
 class Permission
 {
     public string const READ = 'read';
 }
```

And replace it here and in the other place - voter, security PHP config, etc.:

```diff
-#[IsGranted('read')]
+#[IsGranted(Permission::READ)]
 class SomeController
 {
 }
```

Now we can easily jump back and forth between places where the `Permission::READ` is used thanks to IDE.

<br>

To align our codebase to this rule in the present and the future, we add a simple custom PHPStan rule:

```php
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

final class RequireIsGrantedEnumRule implements Rule
{
    public function getNodeType(): string
    {
        return Attribute::class;
    }

    /**
     * @param Attribute $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->toString() !== IsGranted::class) {
            return [];
        }

        $isGrantedExpr = $node->args[0]->value;
        if (! $isGrantedExpr instanceof String_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Instead of "%s" string, use enum constant for #[IsGranted]',
                $isGrantedExpr->value
            ))
            ->identifier('symfony.requireIsGrantedEnum')
            ->build()
        ];
    }
}
```

So we get a nice warning in our CI:

```bash
 --------------------------------------------------------------------------
  src/Controller/SomeController.php:8
 --------------------------------------------------------------------------
  - '#Instead of "read" string, use enum constant for \#\[IsGranted\]#'
  🪪 symfony.requireIsGrantedEnum
 --------------------------------------------------------------------------
```



## 7. Final step: Flip from Sensio to Symfony

Now we have our Sensio attributes in the best shape possible. We use strings only in the place where really necessary. We use an enum constant list for easier jumping back and forth and we have a custom PHPStan rule to enforce it.

First, we make use we use `symfony/security-http` 6.2 where the `#[IsGranted()]` attribute is available.


Then we enable the [`SecurityAttributeToIsGrantedAttributeRector` rule](https://getrector.com/rule-detail/security-attribute-to-is-granted-attribute-rector) in our `rector.php` config:

```php
# rector.php
use Rector\Config\RectorConfig;
use Rector\Symfony\Symfony62\Rector\Class_\SecurityAttributeToIsGrantedAttributeRector

return RectorConfig::configure()
    ->withRules([SecurityAttributeToIsGrantedAttributeRector::class]);
```

And run Rector:

```bash
vendor/bin/rector
```

That's it, now we have native Symfony PHP attributes and also,<br>
the best available syntax possible. Yay!

<br>

Happy coding!
