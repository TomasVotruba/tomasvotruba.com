---
id: 374
title: "How can we Generate Unit Tests - Part 2: Building Scoring Script"
perex: |
    I kicked off the [unit test generator](/blog/how-can-we-generate-unit-tests-part-1-testability-score) idea with the first post a week ago. It was a great [success on Reddit](https://www.reddit.com/r/PHP/comments/103vtkt/how_can_we_generate_unit_tests_part_1_testability/), and I'm happy there is interest in the PHP community.


    I often got asked about the **testability score**.
    How does it work, and how can it be measured in actual code? Well, let's find out.
---

<blockquote class="blockquote text-center">
"Any code you can share on how you find the methods<br>
and give them a score?"
</blockquote>

The best way is to learn by example. Let's evaluate 2 methods with different testability scores.

We're on a blog, so let's render a post and display it to the reader:

```php
final class PostRenderer
{
    public function render(Post $post): string
    {
        $postContents = $post->getTitle();

        $postContents .= PHP_EOL . PHP_EOL;

        $postContents .= $post->getContents();

        return $postContents;
    }
}
```

<br>

```php
final class PostController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {}

    public function detail(id $postId): Response
    {
        $post = $this->postRepository->findById($postId);

        return $this->render('post/detail.twig', [
            'post' => $post,
        ]);
    }
}
```

<br>

Which of those methods will be easier to test?

* `PostRenderer::render()` = X?
* `PostController::detail()` = Y?

<br>

Let's forget we're human, and we can quickly *feel* the answer. But it will also take our attention and actually [suck our cognitive energy](/blog/keep-cognitive-complexity-low-with-phpstan/) to read the code.

Instead, we build an automated script that will evaluate it for us **for any given project in instant speed**.

<br>

## 1. The Raw Find and Parse

These 2 files are somewhere in our project. How do we get to them?

First, we find all PHP files in the project's source code.

```php
$phpFiles = glob('src/**/*.php');
foreach ($phpFiles as $phpFile) {
    // $phpFile
}
```

We have the PHP file paths:

* `/src/Controller/PostController.php`
* `/src/Renderer/PostRenderer.php`

Now we add the all-mighty [php-parser](http://github.com/nikic/PHP-Parser):

```bash
composer require nikic/php-parser
# we use the 4.15.2 version
```

Then we build the parser and parse file:

```php
$parserFactory = new PhpParser\ParserFactory();
$phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7);

// foreach...

/** \PhpParser\Node\Stmt[] $stmts */
$stmts = $phpParser->parse(file_get_contents($phpFile));
```

<br>

## 2. Get all Public Class Methods

The best way to find all public methods is to use the native NodeFinder service:

```php
$nodeFinder = new PhpParser\NodeFinder();

/** @var ClassMethod[] $publicClassMethods */
$publicClassMethods = $nodeFinder->find($stmts, function (Node $node) {
    if (! $node instanceof ClassMethod) {
        return false;
    }

    return $node->isPublic();
});
```

<br>

Now that we have all the public class methods, we can evaluate their **testability score**.

<br>

## 3. Traverse With Node Visitors

Now we have to determine score hits, which will be a red flag for the testability of the public method.

<br>

We have:

* a controller that calls some helper method and internal services
* an internal service that takes a value object and turns it into a string

Would you test a controller action method? I don't know about you, but I have never tested a controller with a PHPUnit test. Let's avoid it.

<br>

We mark **a public method in a controller with high testability score**. But how?

We have nodes so that we can use another php-parser service, a `NodeTraverser`:

```php
use PhpParser\NodeTraverser;

foreach ($publicClassMethods as $publicClassMethod) {
    $nodeTraverser = new NodeTraverser;

    // add scoring node visitors
    $nodeTraverser->addVisitor(...);

    $nodeTraverser->traverse([$publicClassMethod]);
}
```

<br>

## 4. Add Scoring Node Visitor

Scoring node visitor has a straightforward if/else structure:

```php
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Name;

final class ActionControllerNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly TestabilityScoreCounter $testabilityScoreCounter
    ) {
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        // there is no return type → skip it
        if (! $node->returnType instanceof Name) {
            // no return type
            return null;
        }

        $returnedClass = $node->returnType->toString();

        // check against your favorite framework "Response" class name
        if ($returnedClass !== 'Response') {
            return null;
        }

        // now we know the returns a response → give it a penalty of 1000 points
        $this->testabilityScoreCount->increase(1000);
    }
}
```

This is our first scoring node visitor!

In reality, we would also check for `return` inside the class method, which works without return type declaration. Also, we would check the parent class to be sure we have a controller class here. But for practical reasons, we keep it simple.


## 5. Putting Node Traverser and Scoring Together

```php
use PhpParser\NodeTraverser;

$testabilityScoreResults = [];

foreach ($publicClassMethods as $publicClassMethod) {
    $nodeTraverser = new NodeTraverser;

    // add scoring node visitors
    $testabilityScoreCounter = new TestabilityScoreCounter();

    $nodeTraverser->addVisitor(new ActionControllerNodeVisitor($testabilityScoreCounter));
    $nodeTraverser->traverse([$publicClassMethod]);


    // here, we get a testability score for every public method
    $methodName = $publicClassMethod->name->toString();
    $testabilityScoreResults[$methodName] = $testabilityScoreCounter->getScore();
}
```

<br>

Now we have a complete script that:

* finds all PHP files,
* parses them to php-parser nodes,
* finds public methods,
* rates them by a set of testability node visitors

✅

## 6. Show Results for the Public Methods

<img src="/assets/images/posts/2023/testability-score.png" class="mb-3 mt-3 shadow img-thumbnail">

You can now use this script to find the easiest methods to test and also what methods to avoid better.

This script already brings you to value, as **you can learn testing by taking on low-hanging fruit** first. It's handy to learn testing for anyone who still needs to try it. See you next week for another step.

<br>

Happy coding!
