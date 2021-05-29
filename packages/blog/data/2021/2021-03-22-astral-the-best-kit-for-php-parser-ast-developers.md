---
id: 308
title: "Astral - The Best Kit for php-parser AST Developers"
perex: |
    Working with php-parser abstract syntax tree is fun. You can modify [any node](https://github.com/rectorphp/php-parser-nodes-docs) in the tree, change class method names or add new arguments.
    <br><br>
    Such work requires **abstract and [deeply focused thinking](/blog/2018/09/13/your-brain-is-your-garden/)**. But sometimes, all we need is to get a method call name or constant value. That's completely different detailed nitpicking boring thinking...
    <br><br>
    That's where **Astral package helps**.

tweet: "New Post on #php 🐘 blog: Astral - The Best Kit for AST Developers    @rectorphp @phpstan"
---

<blockquote class="blockquote text-center mb-5">
    Keeping simple changes in code,<br>
    simple in AST as well.
</blockquote>

## Supports native php, Rector and PHPStan

The [Astral package](https://github.com/symplify/astral) works for your php-parser code, Rector rules, and PHPStan rules too.

```bash
composer require symplify/astral
```

**A. For PHPStan**, include the config in `phpstan.neon`:

```yaml
includes:
    - vendor/symplify/astral/config/services.neon
```

We use this package in [symplify/phpstan-rules](/blog/2020/12/14/new-in-symplify-9-more-than-110-phpstan-rules/) quite extensively.

<br>

**B. For your own php-parser code or Rector rule**, add Symfony container with Bundle:

```php
// config/bundles.php
return [
    Symplify\Astral\Bundle\AstralBundle::class => ['all' => true],
];
```

Now we have the package installed, so how can we use it in real code?

## 1. Get Node Name

Let's say we have this input code we want to process with AST:

```php
$someMethod->someCall(100);
```

We want to:

- get the name of the method call
- and argument value
- change the value if it is lower than 50

<br>

How can we get a node name in native php-parser? Usually, we have an input method that already passes the node we work with:

```php
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;

final class SomeRule
{
    public function resolveNames(MethodCall $methodCall): array
    {
        if ($methodCall->name instanceof Expr) {
            return [];
        }

        $methodName = $methodCall->name->toString();

        if ($methodCall->var instanceof Variable) {
            return [];
        }

        /** @var Variable $methodCallVariable */
        $methodCallVariable = $methodCall->var;
        if (! $methodCallVariable->name instanceof Expr) {
            return [];
        }

        $methodCallerVariableName = (string) $methodCallVariable->name;

        return [$methodName, $methodCallerVariableName];
    }
}
```

Good, now we have names of variable and the method name:

- `$methodName` → `"someCall"`
- `$methodCallerVariableName` → `"someMethod"`

We haven't even started to write our AST logic, but the code is already pretty complicated. Could we do any better?

<br>

**How does the same logic look with Astral?**

```php
use PhpParser\Node\Expr\MethodCall;
use Symplify\Astral\Naming\SimpleNameResolver;

final class SomeRule
{
    public function __construct(
        // PHP 8.0 promoted property syntax
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolveNames(MethodCall $methodCall): array
    {
        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        $methodCallerVariableName = $this->simpleNameResolver->getName($methodCall->var);

        return [$methodName, $methodCallerVariableName];
    }
}
```

No boiler plate code, overly safe check for `Expr` or node type. The `getName()` just gets the name or `null` if it's not possible, e.g. for magical naming:

```php
$someMethod->{$someMethodName}(100);
```

Make use of other method that save you some work:

```php
$this->simpleNameResolver->isName($node, 'someExpectedName');
$this->simpleNameResolver->isNames($node, ['assertTrue', 'assertFalse']);

// useful for PHPStan class name
$this->simpleNameResolver->getClassNameFromScope($scope);
```

And so on.

## 2. Get Node Value

While writing rules for Rector or PHPStan, we need to know the exact values of the argument. E.g., here we want to get `100`.

```php
$someMethod->someCall(100);
```

How can we get the 1st argument value of our method call with plain php-parser?

```php
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPStan\Node\Constant\ClassConstantFetch;

final class SomeRule
{
    public function resolveFirstArgumentValue(MethodCall $methodCall)
    {
        if (count($methodCall->args) < 1) {
            return null;
        }

        $firstArgValue = $methodCall->args[0]->value;
        if ($firstArgValue instanceof LNumber) {
            return $firstArgValue->value;
        }

        if ($firstArgValue instanceof String_) {
            return $firstArgValue->value;
        }

        if ($firstArgValue instanceof ClassConstantFetch) {
            // ..
        }

        // ...
    }
}
```

You get the idea. We have to also account for non-direct known values like these:

```php
private const LIMIT = 100;

$someMethod->someCall(self::LIMIT);
```

**How does Astral help here?**

```php
use PhpParser\Node\Expr\MethodCall;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class SomeRule
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {

    }
    public function resolveFirstArgumentValue(MethodCall $methodCall)
    {
        if (count($methodCall->args) < 1) {
            return null;
        }

        $firstArgValue = $methodCall->args[0]->value;
        // the 2nd argument is current file path, so Astral can resolve magical constants like __DIR__
        // it is available in both Rector/PHPStan via $scope->getFile()
        return $this->nodeValueResolver->resolve($firstArgValue, __FILE__);
    }
}
```

Straightforward and simple. The `NodeValueResolver` can deal with constant references to another class, with magical `__DIR__` or with `SomeClass::class` references.

## 3. Change the Node

Now we put all three parts together to demonstrate the real power of Astral. **We already know that simple operations in AST are very hard to write**. AST is a low-level language that has to account for various errors.

How to change node in pure php-parser? It's a topic so extensive it would make a standalone post. Fortunately, there is one. I wrote [How to change PHP code with Abstract Syntax Tree](/blog/2017/11/06/how-to-change-php-code-with-abstract-syntax-tree/) 3 years ago. Beware, it's a lot of code simple method rename.

<br>

Here we'll use the handy Astral service `SimpleCallableNodeTraverser`. It's typical to be used in Rector rules, where we need to traverse deeper into nodes.

**Enough theory, let the code talk:**

```php
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Class_;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class SomeRule
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver,
        private SimpleNameResolver $simpleNameResolver,
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser,
    ) {
    }

    // here we traverse class, and we want to process method calls inside it
    public function process(Class_ $class)
    {
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($class, function (Node $node) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            return $this->processMethodCall($node);
        });
    }

    private function processMethodCall(MethodCall $methodCall): ?Node
    {
        // with early return techinque, we skip all the nodes that does not match our needs
        if (! $this->simpleNameResolver->isName($methodCall->name, 'someCall')) {
            return null;
        }

        // we need at least 1 argument
        if (! count($methodCall->args) < 1) {
            return null;
        }

        $argValue = $methodCall->args[0]->value;
        $value = $this->nodeValueResolver->resolve($argValue, __FILE__);
        if (! is_int($value)) {
            return null;
        }

        // is the value lower than 50? skip it
        if ($value <= 50) {
            return null;
        }

        // replaced with 100
        $methodCall->args[0]->value = new LNumber(100);
        return $methodCall;
    }
}
```

That's it!

<br>

## Rule of the Thumb

Too much to absorb? I agree. Let's keep it simple - the most useful service is `SimpleNameResolver`. Try it next time you'll be writing the Rector rule, PHPStan rule, or extension. You'll be surprised how much boilerplate code you can save. Your code will be cleaner and easier to read.

There is a couple of Astral features we haven't check yet. They're all in [README](https://github.com/symplify/astral).

<br>

Happy coding!
