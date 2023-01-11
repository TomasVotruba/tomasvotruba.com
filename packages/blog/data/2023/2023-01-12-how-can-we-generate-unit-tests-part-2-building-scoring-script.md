---
id: 374
title: "How can we Generate Unit Tests - Part 2: Building Scoring Script"
perex: |
    I have kicked of the [unit test generator](/blog/how-can-we-generate-unit-tests-part-1-testability-score) idea a with first post a week ago. It was a great [success on Reddit](https://www.reddit.com/r/PHP/comments/103vtkt/how_can_we_generate_unit_tests_part_1_testability/) and I'm happy there is interest in the PHP community.
    <br><br>
    I often got asked about the **testability score**.
    How does it work and how it can be measured in real code. Well, let's find out.
---

<blockquote class="blockquote text-center">
"Any code you can share on how you find the methods<br>
and give them score?"
</blockquote>

The best way is to learn by example. Lets evaluate 2 methods with different testability score.

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

Let's forget we're human and we can easily *feel* the answer. But it will also take our attention and actually [suck our cognitive energy](/blog/keep-cognitive-complexity-low-with-phpstan/) to read the code.

Instead, we build automated script that will evaluate it for us **for any given project in instant speed**.

<br>

## 1. The Raw Find and Parse

These 2 files are somewhere in our project. How do we get to them?

First we find all PHP files in source code of project.

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

The best way to find all public methods is to use native NodeFinder service:

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

Now we have to determine first testability score hits, that will be red flag for testability of the public method.

We have:

* a controller that calls some helper method and internal services
* a internal service that takes a value object and turns it into a string

Would you test a controller action method? I don't know how about you, but I have never tested a controller with PHPUnit test. I think we should avoid it.

We should mark such public method in a controller with high testability score. How?

We have nodes, so we can another php-parser service, a `NodeTraverser`:

```php
foreach ($publicClassMethods as $publicClassMethod) {
    $nodeTraverser = new PhpParser\NodeTraverser;
    // add scoring node visitors
    $nodeTraverser->addVisitor(...);

    $nodeTraverser->traverse([$publicClassMethod]);
}
```

Scoring node visitor has very simple if/else structure:

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

This is one of possible scoring node visitors. In reality, we would also check for return nodes and class parents, to be sure we have a controller class here. But for practical reasons we keep it simple.

