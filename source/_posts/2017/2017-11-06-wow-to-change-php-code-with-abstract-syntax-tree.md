---
id: 63
layout: post
title: "How to change PHP code with Abstract Syntax Tree"
perex: '''
    Today we can do amazing things with PHP. Thanks to AST and [nikic/php-parser](https://github.com/nikic/PHP-Parser) we can create very **narrow artificial intelligence, which can work for us**.
    <br><br>
    Let's create first its synapse! 
'''
tweet: "Let AST change code for you #php #phpparser #ast #ai" 
tweet_image: "..."

tested: true
test_slug: Ast
---

We need to make clear what are we talking about right at the beginning. When we say "PHP AST", you can talk about 2 things:


### 1. php-ast 

This is native extension which exports the AST internally used by PHP 7.0+. It allows **read-only** and is very fast, since it's native C extension. Internal AST was added to PHP 7.0 by skill-full Nikita Popov in [this RFC](https://wiki.php.net/rfc/abstract_syntax_tree). You can find it on Github under [`nikic/php-ast`](https://github.com/nikic/php-ast).


### 2. PHP AST

This is AST of PHP in Object PHP. It will take your PHP code, turn into PHP object with autocomplete in IDE and **allows you to modify code**. You can find it on Github under [`nikic/PHP-Parser`](https://github.com/nikic/PHP-Parser/). 

Nikita explains [differences between those 2 in more detailed technical way](https://github.com/nikic/php-ast#differences-to-php-parser). Personally I love [this human reason](https://github.com/nikic/PHP-Parser/blob/master/doc/0_Introduction.markdown#what-is-this-for) the most:

<blockquote class="blockquote">
    "Why would I want to have a PHP parser written in PHP? Well, PHP might not be a language especially suited for fast parsing, but processing the AST is much easier in PHP than it would be in other, faster languages like C. Furthermore the people most probably wanting to do programmatic PHP code analysis are incidentally PHP developers, not C developers."
    <footer class="blockquote-footer text-right">Nikita Popov</footer>
</blockquote>


 Which one would you pick? If you're **lazy like me and hate reading code and writing code** over and over again, the 2nd one.
  
  
## What work can `nikic/PHP-Parser` do for us?
 
Saying that, **we skip the read-feature** of this package - it's used by [PHPStan](https://github.com/phpstan/phpstan) or [BetterReflection](https://github.com/Roave/BetterReflection) - and **move right to the writing-feature**. Btw, back in 2012, even [Fabien wanted to use it in PHP CS Fixer](https://github.com/nikic/PHP-Parser/issues/41), but it wasn't ready yet.

### When we say *modify* and *AST* together, what can you brainstorm?

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

The best way to work with Nodes is to **traverse them with [`PhpParser\NodeTraverser`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeTraverser.php)**:

```php
$nodeTraverser = new PhpParser\NodeTraverser;
$traversedNodes = $nodeTraverser->traverse($nodes);
```

Now we traversed all nodes, but nothing actually happened. Do you think we forgot to invite somebody in?

Yes, **we need [`PhpParser\NodeVisitor`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitor.php)** - an interface with 4 methods. We can either implement all 4 of them, or use [`PhpParser\NodeVisitorAbstract`](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/NodeVisitorAbstract.php) to save some work:

```php
use PhpParser\NodeVisitorAbstract;

final class ChangeMethodNameNodeVisitor extends NodeVisitorAbstract
{
}
```

We need to find a `ClassMethod` node. I know that, because I use this package often, **but you can [find all nodes here](https://github.com/nikic/PHP-Parser/tree/master/lib/PhpParser/Node)**. To do that, we'll use `enterNode()` method:

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


To work with **class names, interface names, method names** etc., we need to **use `PhpParser\Node\Name`**.

Oh, I almost forgot, we need to actually **invite visitor to the `NodeTraverser`** like this:
 
```php
$nodeTraverser = new PhpParser\NodeTraverser;
$traversedNodes->addVisitor(new ChangeMethodNameNodeVisitor);
$traversedNodes = $nodeTraverser->traverse($nodes);
```

### 4. Save to File

Last step is saving the file ([see docs](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Pretty_printing.markdown)). We have 2 options here:

<br>

**A. Dumb Saving**

```php
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$newCode = $prettyPrinter->prettyPrintFile($traversedNodes);

file_put_contents(__DIR__ . '/SomeClass.php', $newCode);
```

But this will actually **removes spaces and comments**. How to make it right?

<br>

**B. Format-Preserving Printer**

It requires more steps, but you will have output much more under control.


Without our code, it would look like this:

```php
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter\Standard;

$lexer = new Emulative([
    'usedAttributes' => [
        'comments',
        'startLine', 'endLine',
        'startTokenPos', 'endTokenPos',
    ],
]);

$parser = new Php7($lexer);
$traverser = new NodeTraverser;
$traverser->addVisitor(new CloningVisitor);

$oldStmts = $parser->parse($code);
$oldTokens = $lexer->getTokens();

$newStmts = $traverser->traverse($oldStmts);

// our code start

$nodeTraverser = new NodeTraverser;
$nodeTraverser->addVisitor($nodeVisitor);

$newStmts = $traversedNodes = $nodeTraverser->traverse($newStmts);

// our code end

$newCode = (new Standard)->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
```


Congrats, now you've successfully renamed method to `newName`! 


## Advanced Changes? With Rector!

Do you want to see more advanced operaheretions, like those we [brainstormed in the beginning](#when-we-say-em-modify-em-and-em-ast-em-together-what-can-you-brainstorm)? Look at package I'm working on which should **automate application upgrades** - **[RectorPHP](https://github.com/RectorPHP/Rector)**.


<br>

### This post is Tested

This is the first [tested post](https://pehapkari.cz/blog/2017/01/12/why-articles-with-code-examples-should-be-CI-tested/) I've added to my blog.
 It means **it will be updated as new versions of code used here will appear** → *LTS post* that will work with newer `nikic/php-parser` versions. 
 
 Do you want to see those tests? Just click *Tested* badge in the top. 

<br>

Let me know in the comments, what would you like to read about AST and its Traversing and Modification. I might inspire by your ideas.


Happy traversing!
