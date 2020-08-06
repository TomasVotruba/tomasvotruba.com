---
id: 135
title: "Why and How to Avoid the Memory Lock"
perex: |
    When you close the door of my home, they're closed and you need a key to get in. But what if your door has door handle? You have to also lock them.
    <br><br>
    Instead of just closing the door you have to close the door and *that one more thing*. Why is that a bad thing in the code and how to avoid it?
tweet: "New Post on my Blog: Why and How to Avoid the Memory Lock #cleancode #safecode #php #solid"
---

I started [refactoring *easybook* package](https://github.com/javiereguiluz/easybook/pull/185) last week and I found a few interesting snippets there.

Let's start with a simple question: which of following 7 code snippets **opens space for potential bug** that will take your new colleague 2-3 hours to solve?

<br>

1.

```php
<?php

// ...

foreach ($this->publishingItems as $item) {
   $item['content'] = $this->decorateContent($item['content']);
}
```

<br>

2.

```php
<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
         return ['processEvent'];
    }

    public function processEvent(ItemAwareEvent $itemAwareEvent)
    {
        $item = $itemAwareEvent->getItem();
        $item['content'] = 'new content';
    }
}
```

<br>

3.

```php
<?php

// ...

$this->bookGenerator->setBookDirectory($bookDirectory);
$this->bookGenerator->generate();
```

<br>

4.

```php
<?php

// ...

protected function setUp(): void
{
    $this->epub2Publisher = $this->container->get(Epub2Publisher::class);
}
```

<br>

5.

```php
<?php

class SomeController
{
    public function someAction()
    {
        // ...

        $product = ...;

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
```

*Don't mind the presence of Doctrine in Controller here. That's a code smell we don't look for right now.*

<br>

6.

```php
<?php

use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    /**
     * @var SomeDependency
     */
    private $someDependency;

    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }

    // ...
}
```

<br>

7.

```php
<?php
use PhpParser\NodeTraverser;

class SomeNodeTraverser extends NodeTraverser
{
    /**
     * @var SomeDependency
     */
    private $someDependency;

    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }

    public function process()
    {
        foreach ($this->visitors as $visitor) {
            // ...
        }
    }
}
```

Do you have your favorite number? Good, try to remember it.

The memory-lock **is very difficult to spot**. We owe that to *author blindness* and *thinking heuristics* (brain short-cuts), that limits our ability to work effectively in the chaos or under pressure. If you've read [Thinking, Fast and Slow](https://www.amazon.com/Thinking-Fast-Slow-Daniel-Kahneman-ebook/dp/B005MJFA2W/ref=sr_1_1?ie=UTF8&qid=1535407760&sr=8-1&keywords=Thinking%2C+Fast+and+Slow) (must have for anyone curious about his or her brain false positives) or any other book about social psychology, you know where I'm heading.

## When Expectations are Met

Why it's difficult to spot? **It depends on other code and it might code work if our expectations are met**. What does it mean?

Let's take code snippet 7. Will this work or not? Under what condition?

```php
<?php

foreach ($this->visitors as $visitor) {
    // ...
}
```

[This pull-request](https://github.com/nikic/PHP-Parser/pull/528) reveals the answer.

Now back to your coding:

- Do you want to create a code that works under certain assumption?
- Do you want to remember to do the B after each A?
- Do you want to **keep over 50 of such A-B pairs in your head** every time you open your main project?

I don't. I want to be effective and create a valuable bullet-proof code that can be used only one way. A code that doesn't put the burden on the developer to investigate my code first. A code that is safe to use and cannot be broken (if possible).

<br>

So which of those 7 code snippets above are dangerous?

<br>

*Drum rolls...*

<br>

**Yes, all of them!** I knew you'd reveal my poor confusion.

### Don't make the Programmer Think

I'm taking the title from my favorite [intro UX book](http://sensible.com/dmmt.html) because it really punches the line. **There are 3 groups of memory-locks that could be done better** in the code snippets above.

## 1. Change Array Return

<div class="card">
    <div class="card-body">
        The memory lock: <em>after I change array value, I have to put it back into an original set of an array or return it</em>.
    </div>
</div>

Snippets 1 and 2.

This code would change the `$item['content`] value, but `$this->publishingItems` remains unchanged:

```php
<?php

// ...

foreach ($this->publishingItems as $item) {
    $item['content'] = $this->decorateContent($item['content']);
}
```

### How to Solve it?

```diff
 <?php

 // ...

-foreach ($this->publishingItems as $item) {
+foreach ($this->publishingItems as $key => $item) {
     $item['content'] = $this->decorateContent($item['content']);
+    $this->publishingItems[$key] = $item;
 }
```

**Use object instead**:

```diff
 <?php

 // ...

 foreach ($this->publishingItems as $item) {
-     $item['content'] = $this->decorateContent($item['content']);
+     $item->changeContent($this->decorateContent($item['content']));
 }
```

Objects [consume less memory](https://gist.github.com/nikic/5015323) anyway and **you are safe** - more importantly, **anyone extending this code ever after is safer**.


## 2. Double-Method Call

<div class="card">
    <div class="card-body">
       The memory lock: <em>After I call this method I have to call that method to make it really work</em>.
    </div>
</div>

Snippets 3 and 5.

You also have to think about the C - the order:

```php
$this->bookGenerator->setBookDirectory($bookDirectory);
$this->bookGenerator->generate();
```

vs.

```php
$this->bookGenerator->generate();
$this->bookGenerator->setBookDirectory($bookDirectory);
```

vs.

```php
$this->bookGenerator->generate();
```

### How to Solve it?

Use one method that handles it both:

```diff
 <?php

 // ...

-$this->bookGenerator->setBookDirectory($bookDirectory);
-$this->bookGenerator->generate();
+$this->bookGenerator->generateFromDirectory($bookDirectory);
```

Same applied to code snippet 5:

```diff
 <?php

 // ...

-$this->entityManager->persist($product);
-$this->entityManager->flush();
+$this->productRepository->save($product);
```

I dare you to generate the book or save the product the wrong way now!

## 3. Parent Logic

Snippets 4, 6 and 7.

<div class="card">
    <div class="card-body">
       The memory lock: <em>After I call anything in parent method, I have check if I need to call the constructor to prepare some logic</em>.
    </div>
</div>

This would actually fail:

```php
<?php

foreach ($this->visitors as $visitor) {
    // ...
}
```

Why? Because in `parent::__construct()` is set default value for property with null:

```php
<?php

// ...

public function __construct()
{
    $this->visitors = [];
}
```

### How to Solve it?

In this case just add default value to property itself:

```diff
-private $visitors;
+private $visitors = [];
```

There is even coding [standard fixer for that](https://github.com/symplify/coding-standard#array-property-should-have-default-value-to-prevent-undefined-array-issues).

Also, **do not add any logic to constructor** apart dependency injection. Constructor injection is the main reason to use the constructor in 99 %, so most people probably don't expect any extra logic there. Create extra method instead or decouple a class, put it into the constructor and call the method on it.

**Try to avoid these patterns in code, in door design or in architecture of the application as a whole.** One day some programmer will silently thank you when he or she will find what *memory lock* is (the painful way).

<br>

Do you know any other *memory locks* we should watch out for? Share them in the comment!

<br>

<blockquote class="twitter-tweet" data-lang="cs"><p lang="en" dir="ltr">&quot;90% of the bugs I produced were for one of the two reasons:<br>1. Doing multiple things at one place<br>2. Doing one thing at multiple places&quot; - <a href="https://twitter.com/pseudo_coder?ref_src=twsrc%5Etfw">@pseudo_coder</a></p>&mdash; Programming Wisdom (@CodeWisdom) <a href="https://twitter.com/CodeWisdom/status/998180793385209856?ref_src=twsrc%5Etfw">20. května 2018</a></blockquote>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

Happy brain cells liberation!
