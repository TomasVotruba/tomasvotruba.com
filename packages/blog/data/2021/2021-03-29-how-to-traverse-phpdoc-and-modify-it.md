---
id: 309
title: "How to Traverse PHPDoc and Modify It"
perex: |
    Traversing and modifying PHP code is possible thanks to the amazing tool [php-parser](https://github.com/nikic/PHP-Parser) written by Nikita Popov. Rector can work thanks to **node traverser**, which can get to any node abstract syntax tree. Do you want to replace all numbers with `1000`? I wrote about it in [How to change PHP code with Abstract Syntax Tree](/blog/2017/11/06/how-to-change-php-code-with-abstract-syntax-tree/).
    <br>
    <br>
    But what about docblocks - how **can we rename classes in `@var` annotation or replace `integer` with `int`**?
    <br><br>
    Is that even possible without using complex nested structures or regular expressions?
tweet: "New Post on #php 🐘 blog: How to Traverse PHPDoc and Modify It"
tweet_image: "/assets/images/posts/2021/phpdoc_traverser_tweet.png"
---

Today we look at how to modify docblocks easily. But first, we must learn how [`phpstan/phpdoc-parser`](https://github.com/phpstan/phpdoc-parser) works under the hood.

Let's start with a simple docblock like this:

```php
/**
 * @return int
 */
```

If we parse it with `PhpDocParser`... you know what? Let's try this together:

```bash
composer require symplify/astral
```

<br>

```php
use Symplify\Astral\PhpDocParser\StaticFactory\SimplePhpDocParserStaticFactory;

$values = <<<'PHPDOC'
/**
 * @return int
 */
PHPDOC;

$simplePhpDocParser = SimplePhpDocParserStaticFactory::create();
$phpDocNode = $simplePhpDocParser->parse($values);
```

If we dump the `var_dump($phpDocNode)`, we get roughly following node tree:

```bash
PhpDocNode:
  |- children:
     |- PhpDocTagNode
        |- name: "@return"
        |- value: ReturnTagValueNode
            |- type: IdentifierTypeNode
                |- name: "int"
```

This is classic PHP - object in an object in an object...

## How can we Replace `int` with `string`?

First, we need to get to the `IdentifierTypeNode`. Then we check its value is `"int"`. If so, we change it to `"string"`. Seems pretty straightforward, right?

```php
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

/** @var PhpDocNode $phpDocNode */
foreach ($phpDocNode->children as $phpDocChildNode) {
    if (! $phpDocChildNode instanceof PhpDocTagNode) {
        continue;
    }

    // is this node a @return?
    if (! $phpDocChildNode->value instanceof ReturnTagValueNode) {
        continue;
    }

    // does @return have simple type? here can be any TypeNode
    // e.g. UnionTypeNode, IntersectionTypeNode, CallableTypeNode etc.
    $returnTagValueNode = $phpDocChildNode->value;
    if (! $returnTagValueNode->type instanceof IdentifierTypeNode) {
        continue;
    }

    $identifierName = $returnTagValueNode->type->name;
    if (! $identifierName === 'int') {
        continue;
    }

    // phew... here we can finally change value
    $returnTagValueNode->type = new IdentifierTypeNode('string');
}
```

If we run the code above, we manage to see this change:

```diff
 /**
- * @return int
+ * @return string
  */
```

## Next Level: 2 Nodes

In real life, the type is rarely used in a single location. Let's add `@param`:

```php
/**
 * @param int $age
 * @return int
 */
```

After parsing it, we get this node tree:

```bash
PhpDocNode:
  |- children:
     |- PhpDocTagNode
        |- name: "@return"
        |- value: ReturnTagValueNode
            |- type: IdentifierTypeNode
                |- name: "int"
     |- PhpDocTagNode
        |- name: "@param"
        |- value: ParamTagValueNode
            |- type: IdentifierTypeNode
                |- name: "int"

```

How should we extend the logic above to cover the `@param` tag too?

```diff
 if (
     ! $phpDocChildNode->value instanceof ReturnTagValueNode
+    && ! $phpDocChildNode->value instanceof ParamTagValueNode
 ) {
      continue;
 }
```

Easy pick, right?

<br>

<img src="/assets/images/posts/2020/symplify_monorepo_split.jpg" class="img-thumbnail">


## Next Level: 2 Nodes with Nested Types

Types are can be also compound, here is `UnionTypeNode` with 2 `IdentifierTypeNode` nodes in it:

```php
/**
 * @param int|null $age
 * @return int
 */
```

Now we're getting into the loop of joy. I guess you can imagine how this can become hell coding.

But why should we want to change `int` to `string`? That was simple on purpose so that we can focus purely on example.

In reality, we want to rename the old class to a new one:

```diff
 /**
- * @param OldClass|null $age
+ * @param NewClass|null $age
- * @return OldClass
+ * @return NewClass
  */
```

## So Much Complexity!

You're probably wondering, **why is it so hard to change one node in a docblock?** It is only hard if we try to hard-code solution for every single node.

In reality, there are ~40 classes that inherit from `PHPStan\PhpDocParser\Ast\Node`. We would have to add a check for every single of it, if it has a type, nested typed in some property, and so on.

## What about Node Traverser?

Instead, we could use the same principle as php-parser - **a node traverser** with **node visitors**.

Do you hear about it the first time? Think of it as an analogy to event dispatcher:

- `NodeTraverser` ~= `EventDisptacher`
- `NodeVisitor` ~= `EventSubscriber`

<br>

- 1 NodeTraverser has many NodeVisitors
- 1 EventDispatcher has many EventSubscribers

<br>

- EventSubscribers is waiting for a specific event to happen
- NodeVisitor is waiting for a specific node to come


## The Real Code

In reality, we should run the traverse on `PhpDocNode` to get the result:

```php
use Symplify\Astral\PhpDocParser\PhpDocNodeTraverser;

$phpDocNodeTraverser = new PhpDocNodeTraverser();
$phpDocNodeTraverser->traverse($phpDocNode);

// that's it
```

Oh, we forgot to add the `NodeVisitor`:

```php
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Symplify\Astral\PhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;

final class FirstNodeVisitor extends AbstractPhpDocNodeVisitor
{
    public function enterNode(Node $node) : ?Node
    {
        // here we make clear, what node we're looking for
        if (! $node instanceof IdentifierTypeNode) {
            // null = nothing happen, the original node remains untouched
            return null;
        }

        if ($node->name !== 'int') {
            return null;
        }

        // replace the "int" with "string"
        return new IdentifierTypeNode('string');
    }
}
```

Now we only add `FirstNodeVisitor` to `PhpDocNodeTraverser` ↓

```php
use Symplify\Astral\PhpDocParser\PhpDocNodeTraverser;

$firstNodeVisitor = new FirstNodeVisitor();

$phpDocNodeTraverser = new PhpDocNodeTraverser();
$phpDocNodeTraverser->addPhpDocNodeVisitor($firstNodeVisitor);
$phpDocNodeTraverser->traverse($phpDocNode);

// that's better
```

That's it! **Now every single "int" is turned into "string"**, even in complex doblocks like these:

```php
/**
 * @var array{string, Iterable<int, int>}
 */
```

## How Does it Work? Magic Revealed

The logic is crazy simple - again, credit goes to Nikita Popov, who created the [NodeTraverser](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeTraverser.php) in php-parser.

The `PhpDocNodeTraverser` goes through **every public property** of that node (see [the responsible method](https://github.com/symplify/symplify/blob/58ee4f76a7373b3ae44e4ad608aea5dae5c9b63c/packages/simple-php-doc-parser/src/PhpDocNodeTraverser.php#L52-L83) on Github).

That means if the `ReturnTagValueNode` enters, it will go through there:

```php
$returnTagValueNode->type
$returnTagValueNode->description
```

If `$returnTagValueNode->type` is node, it will go through all its public properties etc.

<br>

Making properties public is a convention in both phpdoc-parser and php-parser, so we can 100 % rely on it.

<br>

Now you know: **how to rename the old class to the new class?**

<br>

Happy coding!
