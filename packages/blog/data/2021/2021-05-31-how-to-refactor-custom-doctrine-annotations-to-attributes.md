---
id: 320
title: "How to Refactor Custom Doctrine Annotations to Attributes"
perex: |
    PHP 8 came with [attributes](https://php.watch/versions/8.0/attributes) 7 months ago. Symfony 5.2 now supports `#[Symfony\Component\Routing\Annotation\Route]` attribute, Nette 3.1 has `#[Nette\DI\Attributes\Inject]` attribute and Doctrine ORM 2.9 is now released with `#[Doctrine\ORM\Mapping\Entity]` attributes.

    You're probably already using those [thanks to Rector](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#annotationtoattributerector). That was the easy part. The more challenging part is **custom `@annotation` classes**. Last weekend I refactored a couple of those, and this is what I found out.

---

**tl;dr;** Do you want to get the job done? Use [`DoctrineAnnotationClassToAttributeRector`](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#doctrineannotationclasstoattributerector). Do you want to understand the step-by-step process of refactoring? Read on.

## Open Source vs. Private Project

Is your project open-source?

```diff
-/**
- * @Annotation
- */
+#[Attribute]
 class SomeAnnotation
 {
 }
```

`composer update` downloads a new version of some package, and suddenly, `@annotations` are not working anymore. What the heck?


It's good to practice to **keep a BC layer for open-source packages** - only add new `#[Attribute]` metadata and keep the old annotations there too:

```diff
 /**
  * @Annotation
  */
+#[Attribute]
 class SomeAnnotation
 {
 }
```

Is your project private, and you're the only one using it? Remove the annotations without doubts.

## Update Targets

The Doctrine provides a `@Target` annotation that specifies where the annotation should be used. That avoids miss-use like this:

```php
use Nette\DI\Attributes\Inject;

#[Inject]
class SomePresenter
{
}
```

The alternative in PHP 8 attribute is `Attribute` argument:

```diff
-use Doctrine\Common\Annotations\Annotation\Target;
+use Attribute;

-/**
- * @Annotation
- * @Target({"METHOD"})
- */
+#[Attribute(Attribute::TARGET_METHOD)]
 class SomeAnnotation
 {
 }
```

Autocomplete will guide you to pick the right one.

## Define Contract in Attribute Constructor

In the past, the contract for annotation classes was very weak:

```php
public function __construct($options = null)
{
}
```

Could you guess what is the class above? The main abstract class for [Symfony Validation annotations](https://github.com/symfony/symfony/blob/9ccd0ad387f0aacf2a1f5673fdcf31dbbef22e35/src/Symfony/Component/Validator/Constraint.php#L108).

With refactoring attributes, we have a chance to rethink the weak contract of annotations:

```php
/**
 * @Annotation
 */
final class Validation
{
    public function __construct(array $values)
    {
        if (isset($values['validatorClass'])) {
            // ..
        }
    }
}

```

Let's not think of a custom attribute as a wobbly docblock string, but as **a strict value object**. Required values must be provided:

```php
#[Attribute]
final class Validation
{
    public function __construct(
        private string $validatorClass,
        private array $parameters = []
    ) {
    }
}
```

You can see that each value is a separated argument instead of one enormous array. This will also help with the following step.

## Make use of Named Arguments

Ok, we already have upgraded all custom annotation classes to attribute ones. Now it's time to update the usage in our code:

```diff
-/**
- * @Validation(RangeBoundariesValidator::class, ['key' => 'value'])
- */
+#[Validation(RangeBoundariesValidator::class)]
```

As you can see, the syntax is very similar. In the first case, it's an `array` that can be anything. But in the second case, it's a value object. But what exactly is first and second value for? Without looking into the value object, we cannot know know... or can we?

```php
#[Validation(validatorClass: RangeBoundariesValidator::class, parameters: ['key' => 'value'])]
```

Thanks to [named argument](https://php.watch/versions/8.0/named-parameters), we can. There is also [a PHPStan rule](https://github.com/symplify/phpstan-rules/blob/main/docs/rules_overview.md#requireattributenamerule) to help with this in code-reviews.

Another advantage of named arguments is that we can click on the name and right to the typed promoted property:

```php
#[Validation(validatorClass: RangeBoundariesValidator::class)]
```

## Refactor Annotation Reader to Native Attributes

Last but not least, we have to upgrade our annotation reader that does not understand attributes yet. In the past, the only way to read custom annotations was to use a combination of reflection and Doctrine annotation reader:

```php
use Doctrine\Common\Annotations\Reader;

class SomeClass
{
    public function __construct(
        private Reader $reader,
    ) {
    }

    public function resolve(ReflectionClass $reflectionClass): array
    {
        return $this->reader->getClassAnnotations($reflectionClass);
    }
}
```

That requires pulling the `doctrine/annotations` package, proper configuration, and a deeper understanding of the package. With PHP 8 attributes, this technology becomes more accessible to the broader community.

We can use bare PHP reflection - and we can also add the `@return` type as a bonus:

```diff
- use Doctrine\Common\Annotations\Reader;

 class SomeClass
 {
-    public function __construct(
-        private Reader $reader,
-    ) {
-    }

+    /**
+     * @return Validation[]
+     */
     public function resolve(ReflectionClass $reflectionClass): array
     {
-        return $this->reader->getClassAnnotations($reflectionClass);
+        return $reflectionClass->getAttributes(Validation::class, ReflectionAttribute::IS_INSTANCEOF);
     }
 }
```

## Remove Annotation Dependency

One last step, and we'll be running 100 % pure PHP 8 attributes:

```bash
composer remove doctrine/annotation
```

That's it!

<br>

Happy coding!
