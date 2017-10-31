---
id: 63
layout: post
title: "How to change PHP code with Abstract Syntax Tree" # todo: rename
perex: '''
    ...
'''
_tweet: "..."
_tweet_image: "..."

tested: true
test_slug: Ast
---

We need to make clear what are we talking about right at the beginning. When we say "PHP ast" There are 2 things that most of us can imagine:


### 1. php-ast 

This is native extension which exports the AST internally used by PHP 7.0+. It allows **read-only** and is very fast, since it's native C extension. Internal AST was added to PHP 7.0 by skill-full Nikita Popov in [this RFC](https://wiki.php.net/rfc/abstract_syntax_tree). You can find it on Github under [`nikic/php-ast`](https://github.com/nikic/php-ast).


### 2. PHP AST

This is AST of PHP in Object PHP. It will take your PHP code, turn into PHP object with autocomplete in IDE and **allows you to modify code**. You can find it on Github under [`nikic/PHP-Parser`](https://github.com/nikic/PHP-Parser/). 

Nikita explains [differences between those 2 in more detailed technical way](https://github.com/nikic/php-ast#differences-to-php-parser). Personally I love [this human reason](https://github.com/nikic/PHP-Parser/blob/master/doc/0_Introduction.markdown#what-is-this-for) the most:

<blockquote class="blockquote">
    "Why would I want to have a PHP parser written in PHP? Well, PHP might not be a language especially suited for fast parsing, but processing the AST is much easier in PHP than it would be in other, faster languages like C. Furthermore the people most probably wanting to do programmatic PHP code analysis are incidentally PHP developers, not C developers."
    <footer class="blockquote-footer text-right">Nikita Popov</footer>
</blockquote>


 Which one would you pick? If you're lazy like me and hate reading code and writing code, the 2nd one.
  
  
## What work can `nikic/PHP-Parser` do for us?
 
Saying that, we skip the read-feature of this package. To drop at least one awesome packages that is using it like that - [PHPStan](https://github.com/phpstan/phpstan). Btw, back in 2012, even [Fabien wanted to use it in PHP CS Fixer](https://github.com/nikic/PHP-Parser/issues/41), but it wasn't ready yet.

### Ok, so when we say *modify* and *AST* together, what can you brainstorm?

- change method name
- change class name
- rename property
- change property to method call
- move method from one class to another
- split class to multiple ones
- refactor `$this->get('name')` to constructor injection in Symfony App
- upgrade App from Symfony 3.0 to 4.0
- refactor Laravel App to Symfony App
- ...

It can to many things for you, depends on how much work you put it it. Today we will try to **change method name**. 


## 4 Steps to Changing a name

### 1. Parse code to Nodes

```bash
composer require nikic/php-rector
```

Create parser and parse the file:

```php

use PhpParser\Parser;
use PhpParser\ParserFactory;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7); # or PREFER_PHP5, if your code is older
$nodes = $parser->parse(file_get_contents(__DIR__ . '/SomeClass.php'));
```


### 2. Find Method Node

A conventions to work with Nodes is to traverse them. We don't need to do that manually with nested `foreach()` shenanigans. Instead we can use [`PhpParser\NodeTraverser`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeTraverser.php):

```php
$nodeTraverser = new PhpParser\NodeTraverser;
$traversedNodes = $nodeTraverser->traverse($nodes);
```

Now we traversed all nodes, but nothing actually happened. Do you think we forgot to invite somebody in?

Yes, we need [`PhpParser\NodeVisitor`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php) - an interface with 4 methods. We can either implement all 4 of them, or use [`PhpParser\NodeVisitorAbstract`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitorAbstract.php) to save some work.

I'm lazy, so:
 
```php
use PhpParser\NodeVisitorAbstract;

final class ChangeMethodNameNodeVisitor extend NodeVisitorAbstract
{
}
```

We need to find a `ClassMethod` node. I know that, because I use this package often, **but you can [find all nodes here](https://github.com/nikic/PHP-Parser/tree/master/lib/PhpParser/Node)**:

To do that, we'll use `enterNode()` method.

```php
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;

final class ChangeMethodNameNodeVisitor extend NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return false;
        }
        
        // so we got it, what now?
    }
}
```


### 3. Change Method Name

No we find it's name and change it!

```php
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

final class ChangeMethodNameNodeVisitor extend NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return false;
        }
        
        $node->name = new Name('newName');
    
        // return node to tell parser to modify it    
        return $node;
    }
}
```


To work with class names, interface names, trait names, method names and so on, we need to use `PhpParser\Node\Name` (at least since `nikic/php-parser` v4; before they were mostly bare strings).

Oh, almost forgot, we need to actually invite visitor to the `NodeTraverser`:
 
```php
$nodeTraverser = new PhpParser\NodeTraverser;
$traversedNodes->addVisitor(new ChangeMethodNameNodeVisitor);
$traversedNodes = $nodeTraverser->traverse($nodes);
```

### 4. Save to File

Last step is saving the file ([see docs](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Pretty_printing.markdown)). We have 2 options here:


### 1. Bare Saving

```php
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$newCode = $prettyPrinter->prettyPrintFile($traversedNodes);

file_put_contents(__DIR__ . '/SomeClass.php', $newCode);
```

But this will actually modify spaces, removes comments and other things, that AST doesn't support. How to make it right?

### 2. Format-Preserving Printer

It requires more steps, but you will have output much more under control.


Without our code, it would look like this:

```php
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter;

$lexer = new Lexer\Emulative([
    'usedAttributes' => [
        'comments',
        'startLine', 'endLine',
        'startTokenPos', 'endTokenPos',
    ],
]);

$parser = new Parser\Php7($lexer);
$traverser = new NodeTraverser();
$traverser->addVisitor(new NodeVisitor\CloningVisitor);

$printer = new PrettyPrinter\Standard;

$oldStmts = $parser->parse($code);
$oldTokens = $lexer->getTokens();

$newStmts = $traverser->traverse($oldStmts);

// our code start

$traversedNodes->addVisitor(new ChangeMethodNameNodeVisitor);
$newStmts = $traversedNodes = $nodeTraverser->traverse($newStmts);

// our code end

$newCode = $printer->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
```


Congrats, now you've successfully renamed method to `newName`! 

Do you want to see more advanced operations, like those we brainstormed in the beginning? Look at package I'm working on which should **automate application upgrades** - **[RectorPHP](https://github.com/RectorPHP/Rector)**.


Let me know in the comments, what would you like to read about AST and it's Traversing and Modification. I might inspire by your ideas.

<br>

Happy traversing!
