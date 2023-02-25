---
id: 342
title: "STAMP #4: How to Run PHPStan Rules on Temporary PHP File"
perex: |
    In the previous post, we finished the conversion of [TWIG template to clean and objective PHP](/blog/stamp-3-how-to-turn-twig-helper-functions-to-origin-object) that PHPStan can analyze.


    Today, we'll discover the last missing pieces of the puzzle. How to run PHPStan rules in temporarily compiled PHP code.

tweet_image: "/assets/images/posts/2021/twig_final_example.gif"

updated_since: "November 2021"
updated_message: |
    Added **PHPStan 1.0** approach with `DerivativeContainerFactory`.
---

Note: **all credit for technique in this post goes to [Michal Lulco](https://twitter.com/lulco)**. He's an impressive developer from Slovakia who comes with innovative, simple ideas that work. It's a scarce combination to see in the world, and I'm very grateful for him.

I'm only putting the idea into words to share. "What trick," you ask?

## From TWIG to PHP

In previous parts, we've managed to compile TWIG to cached PHP code and then clean this PHP code into readable PHP objective code:

```php
{{ meal.title }}
```

↓

```php
echo $meal->getTitle();
```

We have just-in-time input we generated on the fly for PHPStan.

## How to run all PHPStan Rules on Temporary File?

The first option is to run the PHPStan manually on the freshly generated PHP file:

```bash
vendor/bin/phpstan analyze /temp/twig/__TwigTemplate_8a9d1381e8329967...php
```

But that's adding more work to our simple PHPStan workflow:

```bash
vendor/bin/phpstan
```

We're lazy developers, and we don't want to run any command more than once. Also, the CI is lazy and can run just once on the whole codebase.

## Meta-Rule

To achieve that, we create a meta-rule. We register this rule in `phpstan.neon` and run along with other rules:

```php
use PHPStan\Rules\Rule;

final class TwigCompleteCheckRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // ...
    }
}
```

<br>

This rule looks for method calls that render TWIG templates:

```php
$twig->render('some_path.twig');

// in controller
$this->render('templates/some_path.twig', [
    'meal' => $meal
])
```

The TWIG templates are converted to temporary PHP content and analyzed... how?

## The `processNode()` Method

In the meta-rule, we find the TWIG template paths, convert them to a temporary PHP file and feed PHPStan to analyze it.

Let the code speak for itself:

```php
use PhpParser\Node\Expr\MethodCall;

final class TwigCompleteCheckRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // 1. here we detect if it's Twig render method call
        if (! $this->isTwigRenderMethodCall($node)) {
            // skip if not
            return []
        }

        // 2. compile TWIG to PHP
        $temporaryPHPFileContent = $this->twigToPHPCompiler->compile($node);

        // 3. PHPStan needs physical file, so we dump string to temporary file
        file_put_contents('temporary_file.php', $temporaryPHPFileContent);

        // 4. pseudo-code! feed PHPStan the temporary file
        $foundErrors = $this->phpstanAnalyzer->analyzeFile($temporaryPHPFileContent);

        // 5. return errors found in this file
        return $foundErrors;
    }
}
```

Pretty straightforward. All the steps are already working, except number 4.

## How can we Feed PHPStan the Temporary File?

The dependency injection mantra says, "if you want something, ask for it in the constructor". The PHPStan service that analyzes files is called... `PHPStan\Analyser\FileAnalyser`.

The `FileAnalyser` has single public method [`analyzeFile()`](https://github.com/phpstan/phpstan-src/blob/2c1107588603afaa8cd3e97165b7eb1736cb4393/src/Analyser/FileAnalyser.php#L59), with 4 required parameters.

## ~~Ask for It~~ Create New Container With Fresh Instance!

Now, we could ask for `PHPStan\Analyser\FileAnalyser` in the constructor as any other service. But that will lead to side-effects and bugs. Instead, Ondrej Mirtes advised me to use own instance with help of `DerivativeContainerFactory`:

```php
use PHPStan\DependencyInjection\DerivativeContainerFactory;

public function __construct(
    private DerivativeContainerFactory $derivativeContainerFactory
) {
}

public function analyse()
{
    // @todo add cache to create just once
    $container = $this->derivativeContainerFactory->create([__DIR__ . '/../config/php-parser.neon']);
    $fileAnalyser = $container->getByType(FileAnalyser::class);
}
```

PHPStan 1.0 uses [various of php-parser versions](https://github.com/rectorphp/rector/issues/6744#issuecomment-950282826), depending on use case - some are optimized for cache, some for performance and some for deep analysis. Saying that, we need to ask for the right one in our custom config:

```yaml
// config/php-parser.neon
services:
    defaultAnalysisParser:
        factory: @cachedCurrentPhpVersionRichParser
        arguments!: []

    cachedCurrentPhpVersionRichParser:
        class: PHPStan\Parser\CachedParser
        arguments:
            originalParser: @currentPhpVersionRichParser
            cachedNodesByStringCountMax: 1024
        autowired: no
```

Now we created custom container with fresh `PHPStan\Analyser\FileAnalyser` that will work exactly for our use case!

## Make use of FileAnalyser

Let's combine the parts together:

```diff
 use PhpParser\Node\Expr\MethodCall;
+use PHPStan\DependencyInjection\DerivativeContainerFactory;
+use PHPStan\Analyser\FileAnalyser;
+use PHPStan\Rules\Registry;

 final class TwigCompleteCheckRule implements Rule
 {
+    public function __construct(
+        private DerivativeContainerFactory $derivativeContainerFactory,
+        private Registry $registry,
+    ) {
+    }

     public function getNodeType(): string
     {
         return MethodCall::class;
     }

     /**
      * @param MethodCall $node
      */
     public function processNode(Node $node, Scope $scope): array
     {
         // 1. here we detect if it's Twig render method call
         if (! $this->isTwigRenderMethodCall($node)) {
             // skip if not
             return []
         }

         // 2. compile TWIG to PHP
         $temporaryPHPFileContent = $this->twigToPHPCompiler->compile($node);

         // 3. PHPStan needs physical file, so we dump string to temporary file
         file_put_contents('temporary_file.php', $temporaryPHPFileContent);

         // 4. feed PHPStan the temporary file
-        $foundErrors = $this->phpstanAnalyzer->analyzeFile($temporaryPHPFileContent);
+        $container = $this->derivativeContainerFactory->create([
+            __DIR__ . '/../config/php-parser.neon'
+        ]);
+
+        $fileAnalyser = $container->getByType(FileAnalyser::class);
+        $fileAnalyserResult = $fileAnalyser->analyseFile(
+            $temporaryPHPFileContent, [], $this->registry, null
+        );

         // 5. return errors found in this file
-        return $foundErrors;
+        return $fileAnalyserResult->getErrors();
     }
 }
```

<br>

One service, one method. Nice and clean design in practice.
All is looking good. Let's run PHPStan:

```bash
vendor/bin/phpstan
```

↓

PHPStan crashes with following error:

```bash
InvalidStateException: Circular reference detected for services: 0282, registry.
```

<br>

That's a pickle!

## Circular Rule References

The "registry" service is used in some dependency injection that is injected in a circle. We're trying to run `TwigCompleteCheckRule`, which asks for `PHPStan\Rules\Registry`. How does the [`PHPStan\Rules\Registry` constructor](https://github.com/phpstan/phpstan-src/blob/2c1107588603afaa8cd3e97165b7eb1736cb4393/src/Rules/Registry.php#L17) look like?

```php
/**
* @param \PHPStan\Rules\Rule[] $rules
*/
public function __construct(array $rules)
{
    // ...
}
```

<br>

Aha! So then we have the full circle:

* `PHPStan\Rules\Registry` asks for all rules in the constructor
* one of all rules is `TwigCompleteCheckRule`
* the `TwigCompleteCheckRule` asks for `PHPStan\Rules\Registry` in the constructor
* `PHPStan\Rules\Registry` asks for all rules in the constructor
* ...

## Stepping out of the Circle

Seeing this, we cannot use `PHPStan\Rules\Registry` in our `TwigCompleteCheckRule` rule. What else can we do? We need to get the list of all rules, except this one. **Get ready for the trick!** I tried to get here but failed. I was amazed when I saw how Lulco solved this elegantly.

In our single rule, we need all the other rules. `PHPStan\Rules\Registry` is just injected service; it's a wrapper object. We can unwrap this object!

```diff
 use PHPStan\Rules\Registry;
+use PHPStan\Rules\Rule;

 final class TwigCompleteCheckRule implements Rule
 {
+    private Registry $registry;

     /**
      * @param Rules[]
      */
     public function __construct(
-        private Registry $registry,
+        array $rules,
+    ) {
+        $this->registry = new Registry($rules);
+    }

     // ...
 }
```

<br>

That's it? Let's try to run the PHPStan rule and see if it works:

<img src="/assets/images/posts/2021/twig_final_example.gif" class="img-thumbnail">

## 🥳️🥳️🥳️

<br>

Once more time, [huge thanks to Lulco](https://twitter.com/lulco), who made all this possible!<br>
Also, thank you [Ondra Mirtes](https://twitter.com/ondrejmirtes) for PHPStan 1.0 tips with custom `PHPStan\Analyser\FileAnalyser`.

<br>

That's all for TWIG templates, and that's all for theory. In the next post, we'll look at more practical. You'll learn how to run such a rule **in your codebase**.

<br>

Happy coding!
