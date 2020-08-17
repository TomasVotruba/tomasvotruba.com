---
id: 99
title: "How I Got into Static Trap and Made Fool of Myself"
perex: |
    PHP story with code examples, copy-cat killers, just a little bit of static, consistency, sniffs and way to prevent all that ever happening ever again.
tweet: "New Post on #lazyprogrammer Blog: How I Got into Static Trap and Made Fool of Myself"
---

Today the format will be reversed - first I'll show you practical code and its journey to legacy, then theory takeaways that would save it.

[Symplify\CodingStandard](https://github.com/symplify/coding-standard) contains complex Sniff and Fixers like [the doc block cleaner](/blog/2017/12/17/new-in-symplify-3-doc-block-cleaner-fixer). Job of `RemoveUselessDocBlockFixer` is clear - **remove any doc block that has no extra value over the php code itself**:

```diff
 /**
- * @param int $value value instance
- * @param $anotherValue
- * @param SomeType $someService A SomeType instance
- * @return array
  */
 public function setCount(int $value, $anotherValue, SomeType $someService): array
 {
 }
```

The goal is clear, but **how does it work beneath the surface**? There are multiple steps that Fixer needs to perform one by one:

- find a method
- find its docblock
- find parameters in method code, detect their names and types
- compare them to docblock
- judge value of description (e.g. "a Type instance" has no value)
- remove those that were found useless

Is that all? Nope. **There also code that handles php docs**:

- doc block parser that can parse any doc comment
- that can handle invalid and non-standard formats
- and a doc block printer, that can keep the original spacing

Just a reminder, this all started with a simple idea:

```diff
-/**
- * @param int $value
- */
 public function compute(int $value)
 {
 }
```

Today I'll write about **how code always grows and that we should anticipate it and code the best way we know right from the beginning**. And what happened to me when I thought I could handle it by *using static methods only where it makes sense* (well, everything makes sense, until it's legacy drowning you down).

## Story Of Static Growth

Let's look how the fixer grew to the point it turned into legacy, how that shot myself and what I could (and will) do better to prevent it.

(To skip irrelevant details, I'll use pseudocode instead of [original full code](https://github.com/symplify/symplify/blob/5603ed130bfd29bfdad050b7726b9c8e65a558fd/packages/CodingStandard/src/Fixer/Commenting/RemoveUselessDocBlockFixer.php).)

```php
class RemoveUselessDocBlockFixer
{
    public function fix($tokens)
    {
        foreach ($tokens as $token) {
            if (! $token->isMethod()) {
                continue;
            }

            // it's a method!
            $docBlock = $this->getDocBlockByMethod($token);
            if ($docBlock === null) {
                continue;
            }

            // it has a doc block!
            $this->removeUselessContentFromDocBlock($docBlock);
        }
    }
}
```

That's a basic workflow. In Easy Coding Standard 3 and below, checkers have no constructor injection, only `new` and `::static` methods were allowed. I took this inspiration from [PHP CS Fixer where `new` is a first class citizen](https://github.com/FriendsOfPHP/PHP-CS-Fixer/search?utf8=%E2%9C%93&q=new+TokensAnalyzer&type=). There is no DI container, just static instantiations. Maybe that should warn me, but I said to myself "it's a popular package, it has new fixers from time to time and it's tagged once a while, it must be good and they know what they're doing".

So back to the code:

```php
public static function getDocBlockByMethod($token)
{
    $docBlockPosition = DocBlockFinder::find($token);
    if ($docBlockPosition === null) {
        return null;
    }

    return DocBlockFactory::createFromPosition($docBlockPosition);
}
```

```php
public static function removeUselessContentFromDocBlock($docBlock)
{
    DocBlockCleaner::processParamAnnotations($docBlock);
    DocBlockCleaner::processReturnAnnotations($docBlock);
}
```

### Static with 3rd Party Code?

You see where it goes. The biggest potential black hole is always 3rd party code (unless it's your code). I could write the docblock parser myself or make use of [phpDocumentor/ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock). It was the best on the market in that time. Neither ready for PHP 5.5+ features like variadics nor formatter preserving printer. Except that it worked pretty well.

```php
class DocBlockFactory
{
    public static function createFromPosition($docBlockPosition)
    {
        $tagFactory = new StandardTagFactory($fqsenResolver, [
            'param' => TolerantParam::class, // own overloaded class
            'return' => TolerantReturn::class, // own overloaded class
            'var' => Var_::class, // own overloaded class
        ]);

        $descriptionFactory = new DescriptionFactory($tagFactory);
        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        $phpDocumentorDocBlockFactory = new DocBlockFactory($descriptionFactory, $tagFactory);

        return $phpDocumentorDocBlockFactory->create($docBlockPositoin);
    }
}
```

So every time a single doc block is created, more than 10 classes (counting these on background) are created too. It might be a small deal for performance, but even bigger for legacy code smell that might hit me back later. But whatever, YOLO!

And here all the static fun ends. Well, not yet, because it worked. I talked a lot with the maintainer of `phpDocumentor/ReflectionDocBlock` about moving it forward, but as I was the only one trying, it didn't lead much further than issue chats and PRs that were opened for too long. It was only logical that without [monorepo](/clusters/#monorepo-from-zero-to-hero) all the time was swallowed only by maintenance of 4 interdependent packages.

### A New Shiny Package?

Then [Jan Tvrdík](https://github.com/JanTvrdik) came with a support package for PHPStan for handling php docs - [phpstan/phpdoc-parser](https://github.com/phpstan/phpdoc-parser). It is built on similar principles as `nikic/php-parser`, much younger and robust.

I thought: "I'd like to try that one package in my code", but how?

It's easy, just replace all the old static classes with new ones:

```php
class DocBlockFactory
{
    public static function createFromPosition($docBlockPosition)
    {
        $content = $this->getContentOnPosition($docBlockPosition);

        $lexer = new Lexer;
        $tokenIterator = new TokenIterator($lexer->tokenize($content));

        $phpStanPhpDocParser = new PhpStanPhpDocParser(new SomeDependency(new AnotherDependency));

        return $phpStanPhpDocParser->parse($tokenIterator);
    }
}
```

### Adding Depedency to Static Hell Tree

Do you need to add whitespace config? Just add it in every layer... or make it also static.

```diff
 class DocBlockFactory
 {
     public static function createFromPosition($docBlockPosition)
     {
         $content = $this->getContentOnPosition($docBlockPosition);

         $lexer = new Lexer;
         $tokenIterator = new TokenIterator($lexer->tokenize($content));

         $phpStanPhpDocParser = new PhpStanPhpDocParser(new SomeDependency(new NewAnotherDependency));

-        return $phpStanPhpDocParser->parse($tokenIterator);
+        $docBlock = $phpStanPhpDocParser->parse($tokenIterator);
+        $docBlock->addWhitespaceConfig($this->whitespaceConfig);
+
+        return $docBlock;
    }
+
+    public function setWhitespaceConfig(WhitespaceConfig $whitespaceConfig)
+    {
+        $this->whitespaceConfig = $whitespaceConfig;
+    }
}
```

But what if you forget to add it

```diff
+    public function ensureWhitespaceConfigIsSet()
+    {
+        if ($this->whitespaceConfig) {
+            return;
+        }

+        throw new WhitespaceConfigNotSetException(sprintf('Informative message in "%s" method', __METHOD__));
+    }
```

Congratulations, you've just made a static container all over your code, similar to Laravel Facades.
Uff, I just get headache by writing this code.

But why stopping there? Let's add a configuration that will tell the `DocBlockFactory` if the starting tag should be `/*` or `/**`.

Well, shoot me now!

## How to Get From Static Hell?

### 1. Dependency Injection ~~First~~ Only

Dependency injection First. Not first, but **only** dependency injection.

I told myself - "here the static method makes sense, it's just one little method". The problem is, that static methods work well only with other static methods. You simply can't inject a service to a class with static methods and use it statically.. well, to be honest, Laravel did it in facades and Reflections, but you should not. Unless you want to use such approach in the whole codebase. That would be the only valid reason to do it so.

**So be consistent in architecture pattern you pick.**

It took me [3](https://github.com/symplify/symplify/pull/680) [pull](https://github.com/symplify/symplify/pull/693) [requests](https://github.com/symplify/symplify/pull/723) to get out of this mess. Not to try the new package, just to prepare the code to be able to do so. Instead, I could have a clear DI design, use one PR at a time to trying this package and other 2 PRs could have been new features.

### 2. Beware Your Inner Copy-Cat Coder

> A copycat crime is a criminal act that is modeled or inspired by a previous crime that has been reported in the media or described in fiction.

This all started with social learning - "children see, children do". I saw static approach in Fixers in PHP CS Fixer and I was making a Fixer. So why not use it? I felt in my guts it's not the best way to go, but I was not sure why and I didn't see anybody else using DI in CLI applications. Now I know why.

If you ever have a feeling that there is a better way to do things but you'll see that some Tomas Votruba is doing it differently, take your time - **trust yourself, your intuition guides you for a reason**. Question him and propose your idea, even though it might be crazy at the start. Maybe you'll save yourself and him a few PRs and many frustrated days from climbing up the legacy hole.

### 3. Sniff It - Setup and Forget

To prevent this 10 hours of trauma happening ever again, I made `NoClassWithStaticMethodWithoutStaticNameRule` that will look after our code.

<em class="fas fa-fw fa-times text-danger fa-lg"></em>

```php
class SomeClass
{
    public static function someFunction()
    {
    }
}
```

<em class="fas fa-fw fa-check text-success fa-lg"></em>

```php
class StaticSomeClass
{
    public static function someFunction()
    {
    }
}
```

I've added this sniff to set before refactoring, scanned the code and [added all found files to ignored](https://github.com/symplify/symplify/pull/722/files#diff-a8b950982764fcffe4b7b3acd261cf91R84). That way I knew what all classes need refactoring.

### 4. Remove `Static` from Methods - One Step at a Time

I always do this in one single PR, starting with the simplest factory from ignored files above.

Remove the `static` in one factory:

```diff
 class UseImportsTransformer
 {
-    public static function addNamesToTokens(...)
+    public function addNamesToTokens(...)
 }
```

Pass it via constructor:

```diff
 class RemoveUselessDocBlockFixer
 {
+    /**
+     * @var UseImportsTransformer
+     */
+    private $userImportsTransformer;
+
+    public function __construct(UseImportsTransformer $userImportsTransformer)
+    {
+        $this->userImportsTransformer = $userImportsTransformer;
+    }
 }
```

And use it in code:

```diff
-UseImportsTransformer::addNamesToTokens($this->newUseStatementNames, $tokens);
+$this->useImportsTransformer->addNamesToTokens($this->newUseStatementNames, $tokens);
```

### 5. Keep Your Environment Clean

I also admit that another code smell lead to this. In Symplify and Rector there is used [Symfony 3.3 services architecture](/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3) with autowiring and autodiscovery. State of art in PHP DI at the moment.

But Fixers and Checkers were exceptions. They were registered as services, **but not autowired**. So I was used to not-to add dependency to them manually, but via setters, `new` or `::static`. It eventually and logically leads to this situation.

I learned something new and [migrated to full-service approach in ECS 4](/blog/2018/03/26/new-in-easy-coding-standard-4-clean-symfony-standard-with-yaml-and-services).


## 3 Takeaways You Should not Take Statically

- Static is not only `::method()`, but also `new <class>` and `::create()`.
- Use dependency injection or static methods, not a mixture. **Be consistent** everywhere in your code, or it will eventually backfire.
- There is no best way to do things, **you just have to experience limits of various approaches and use the one that performs the best**. And re-evaluate.

<br><br>

They also say that:

<blockquote class="blockquote">
    Wisdom is an ability to learn from others' mistakes.
</blockquote>

So I hope you learned something new today!
