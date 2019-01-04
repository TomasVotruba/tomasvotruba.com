---
id: 173
title: "How to Complete Type Declarations without Docblocks with Rector"
perex: |
    In [previous post](/blog/2018/12/10/rocket-science-behind-migration-of-docblock-types-to-php-typehints/) we looked at how to migrate from docblocks to type declarations. From `@param Type $param` to `Type $param`. Even if it doesn't break code like coding standards do, works with inheritance, localized `self` and `static` and propagates types to all child classes, it's still not such a big deal.
    <br><br>
    But **how do you complete type declarations if don't have any docblocks?**  
tweet: "New Post on #php 🐘 blog: How to Complete Type Declarations without Docblocks with #Rectorphp"
tweet_image: "/assets/images/posts/2019/type-declarations/peek.gif"
---

Well, you're doomed, because **you should write** [docblocks everywhere where useful](/blog/2017/12/17/new-in-symplify-3-doc-block-cleaner-fixer/).

```php
<?php

class SomeClass
{
    public function getItems()
    {
        return ['Statie', 'EasyCodingStandard', 'Rector'];
    }    
}
```

If you only typed `@return` annotation 4 years ago, where no-one thought there will ever be scalar type declarations and such annotations were only for geeks. Transition to PHP 7 features would be so easy:

```diff
 <?php

 class SomeClass
 {
-    public function getItems()
+    public function getItems(): array
     {
         return ['Statie', 'EasyCodingStandard', 'Rector'];
     }    
 }
```

Now, I hope you've learned, that **future compatibility is a thing** and you'll write annotations to each property, so they Rector will [convert them to type declarations](/blog/2018/11/15/how-to-get-php-74-typed-properties-to-your-code-in-few-seconds/#visualize-future-compatibility) once PHP 7.4 is out.

## "It's Not My Fault"

Well, but what if you've inherited such legacy code, are you to blame? No, **no-one is to blame even if you wrote the code**. That's how change works. **We feel like we know all today, but in 5 years array-type declarations will be considered code smell** and we keep it only *for historical reasons*. 
 
So how can we fight the change together?

```php
<?php

class SomeClass
{
    public function getItems()
    {
        $items = ['Statie', 'EasyCodingStandard', 'Rector'];
        
         return $this->sortItems($items);
    }
    
    private function sortItems(array $items)
    {
        sort($items);
        return $items;
    }  
}
```

How would upgrade this code to type declarations?

```diff
 <?php

 class SomeClass
 {
-    public function getItems()
+    public function getItems(): array
     {
         $items = ['Statie', 'EasyCodingStandard', 'Rector'];

         return $this->sortItems($items);
     }
    
-    private function sortItems(array $items)
+    private function sortItems(array $items): array
     {
         sort($items);
         return $items;
     }  
 }
```

Good job, that's correct! Now you can do it for the rest of your 25 000 lines of code.

Or... You can be *lazy smart * and **use static analysis from PHPStan to do it for you**. It already knows that:

- `$items` is `array` with strings 
- `sort` sorts `array` and keeps it an `array`

That was too easy... let's check case we'll find in our code:

```diff
 <?php
 
 // ...

-public function getResult()
+public function getResult(): float
 {
     if (true) {
         return 5.2;
     }
    
     $value = 5.3;
    
     return $value;
 }
```

Don't get confused by incorrect doc:

```diff
 /**
  * @return bool
  */
-public function getNumber()
+public function getNumber(): int
 {
     return 5;
 }
```

Is one object or null?

```diff
 <?php
 
 // ...

-public function resolve(Product $product)
+public function resolve(Product $product): ?Product
 {
     // ...
     
     if (...) {
         return null;
     }

     return $product;
 }
```

<br>

<blockquote class="blockquote text-center mb-5 mt-5">
    If PHPStorm or PHPStan knows what type it is,<br> 
    there is a big chance Rector can change it everywhere in your code.
</blockquote>

<br>

[Honza Kuchař](https://github.com/jkuchar) spend one afternoon over Rector with me and motivated me to add this feature to Rector, so PHP developers can enjoy the laziness AST provides. Thank you, Honza! It's done ([see PR](https://github.com/rectorphp/rector/pull/880)).

Just setup [Rector](https://github.com/rectorphp/rector) and upgrade your code:

```yaml
# rector.yml
services:
    Rector\Php\Rector\FunctionLike\ReturnTypeDeclarationRector: ~
```
```bash
vendor/bin/rector process src 
```

Done!

<br>

Just one more thing...

## Properties Without `@var`?

There is a big chance that if our code misses `@param` and `@return` type declarations, the `@var` is missing as well. We don't have to go to legacy code to find such code.

Do you recognize this class?

```php
<?php

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Application;

class Command
{
    private $application;
    private $name;
    private $processTitle;
    private $aliases = [];
    private $definition;
    private $hidden = false;
    private $help;
    private $description;
    private $ignoreValidationErrors = false;
    private $applicationDefinitionMerged = false;
    private $applicationDefinitionMergedWithArgs = false;
    private $code;
    private $synopsis = [];
    private $usages = [];
    private $helperSet;
    
    // ...
    
    public function setApplication(Application $application = null)
    {
        $this->application = $application;
    }
}
```

Quiz question: how would **you utilize AST to autocomplete all `@var` annotations to properties above**?

<br> 

`$this->application = $application;` we know that `$application` can be upgraded to:

```diff
+/**
+ * @var \Symfony\Component\Console\Application
+ */ 
 private $application;
``` 

That's it! Let's put this algorithm into Rector:

```yaml
# rector.yml
services:
    Rector\Php\Rector\Property\CompleteVarDocTypePropertyRector: ~
```

```bash
vendor/bin/rector process src 
```

↓

```diff
 <?php

 namespace Symfony\Component\Console\Command;

 use Symfony\Component\Console\Application;

 class Command
 {
+    /**
+     * @var \Symfony\Component\Console\Application
+     */
     private $application;
+    /**
+     * @var string
+     */
     private $name;
+    /**
+     * @var string
+     */
     private $processTitle;
+    /**
+     * @var mixed[]|string[]
+     */
     private $aliases = [];
+    /**
+     * @var \Symfony\Component\Console\Input\InputDefinition
+     */
     private $definition;
+    /**
+     * @var bool
+     */
     private $hidden = false;
+    /**
+     * @var string
+     */
     private $help;
+    /**
+     * @var string
+     */
     private $description;
+    /**
+     * @var bool
+     */
     private $ignoreValidationErrors = false;
+    /**
+     * @var bool
+     */
     private $applicationDefinitionMerged = false;
+    /**
+     * @var bool
+     */
     private $applicationDefinitionMergedWithArgs = false;
+    /**
+     * @var callable
+     */
     private $code;
+    /**
+     * @var mixed[]
+     */
     private $synopsis = [];
+    /**
+     * @var mixed[]
+     */
     private $usages = [];
+    /**
+     * @var \Symfony\Component\Console\Helper\HelperSet
+     */
     private $helperSet;
```

In matter of seconds now we have:

- Better static type analysis <em class="fas fa-lg fa-check text-success"></em>
- Better PHPStorm autocomplete <em class="fas fa-lg fa-check text-success"></em>
- Better coding standards <em class="fas fa-lg fa-check text-success"></em>
- No manual boring work required from the developer <em class="fas fa-lg fa-check text-success"></em>

<br>

If you want to how Rector works behind this scene, ([check the PR](https://github.com/rectorphp/rector/pull/885)). **The code size to make this happen is not what you'd expect** thanks to php-parser elegance (thanks Nikic!).

<br>

What more could be done to turn legacy code to elegant code? Share in comments, I'm eager to know.  

<br>

Happy coding!
