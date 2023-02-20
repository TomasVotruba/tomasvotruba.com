---
id: 354
title: "How to Upgrade Latte&nbsp;2&nbsp;Macro to&nbsp;Latte&nbsp;3&nbsp;Tag"
perex: |
    Latte 3 running on abstract syntax tree was released this week. Do you want to upgrade? First, check [5 steps to get ready](/blog/5-steps-to-get-ready-for-latte-3).


    Do you have any custom macros in your project? They're the biggest challenge of the Latte 3 upgrade. But don't worry, today we'll rewrite them together.

tweet: "New Post on the 🐘 blog: How to Upgrade Latte 2 Macro to Latte 3 Tag      #nettefw"
---

## For Early Adopters

Note: this is an early way to upgrade macro to Latte 3 based on trial/error, and it might improve based on to-be-released documentation.
Suppose you get stuck with your macro, [ask in the dedicated topic on Nette Forum](https://forum.nette.org/cs/35141-latte-3-nejvetsi-vyvojovy-skok-v-dejinach-nette).

<br>

Latte macro is a Latte "shortcut" that unwraps to compiled PHP code of the cached template, and it is compiled once, re-used, and improves the performance. In Latte 3, they're called *tags*.

That's enough for theory. Now we check [upgrade of actual code in Amateri](https://www.startupjobs.cz/nabidka/24580/hleda-se-senior-php-programator-se-zapalem-pro-vec).

<br>

A macro that inlines SVG files right to HTML code. From macro in Latte file...

```html
{embeddedSvg "circle.svg"}
{embeddedSvg "circle.svg", "class" => "blue"}
```

<br>

...to compiled template PHP code:

```html
echo '<svg width="10" height="10"><circle cx="4.5" cy="4.5" r="3.5"/></svg>';
echo '<svg width="10" height="10" class="blue"><circle cx="4.5" cy="4.5" r="3.5"/></svg>';
```

## Before We Start...

We can upgrade the first macro to Latte 3 today, but in the case of 2nd, 3rd... or external dependency, it might take some time. We could stay in a traffic jam between both versions for a few weeks.

<br>

To support both Latte 2 macros and Latte 3 tags, use [following trick](https://forum.nette.org/cs/35141-latte-3-nejvetsi-vyvojovy-skok-v-dejinach-nette?p=2#p220012):

```php
if (version_compare(Latte\Engine::VERSION, '3', '<')) {
    // Latte 2
    $this->latte->onCompile[] = function { ... };
} else {
    // Latte 3
    $this->latte->addExtension(...);
}
```

## 1. From DI extension ot Latte Extension

Huh, what is this `addExtension()` method? Latte 3 uses its own extensions. No, it is not a usual a`CompilerExtension` as we know it.

What does it look like for our embeddedSvg macro, then?

```php
namespace App\Latte;

use Latte\Extension;

final class EmbeddedSvgLatteExtension extends Extension
{
    // here we pass the configuration from config
    public function __construct(
        private string $baseDir
    ) {
    }
}
```

<br>

Then we replace the DI extension with the Latte extension in `config.neon`:

```diff
-extensions:
-    embeddedSvg: App\Latte\DI\EmbeddedSvgExtension
-
-embeddedSvg:
-    baseDir: %wwwDir%/images

+latte:
+    extensions:
+        - App\Latte\EmbeddedSvgLatteExtension(baseDir: %wwwDir%/images)
```

<br>

We've just added Latte 3 extension that loads itself and does nothing. Time to add the first node!

## 2. From Macro to an AST Node

Abstract syntax tree landed in Latte Three. What does it mean for macros? The macro set is gone, and we'll create our own node class instead. Let's call it `EmbeddedSvgNode`.

First, we register it in our newly created Latte extension in the `getTags()` method:

```php
namespace App\Latte;

use Latte\Extension;
use Latte\Compiler\Tag;
use App\Latte\Node\EmbeddedSvgNode;

final class EmbeddedSvgLatteExtension extends Extension
{
    public function __construct(
        private string $baseDir
    ) {
    }

    public function getTags(): array
    {
        return [
            'embeddedSvg' => function (Tag $tag): EmbeddedSvgNode {
                return new EmbeddedSvgNode($tag, $this->baseDir);
            },
        ];
    }
}
```

How should we read this? Everytime we call `{embeddedSvg ...}` in Latte, the `new EmbeddedSvgNode($tag, $this->baseDir)` will be created on that place.

We can pass any arguments into its constructor. In our case:

* `$tag` that holds every information from Latte, e.g., the file path argument
* `$this->baseDir` path that leads to a base directory with all images

<br>

## 3. From `Macro::open()` to the AST Node

In the old Latte 2 macro, we used an `open()` method that handles both input parsing and node printing:

```php
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class EmbeddedSvgMacro extends MacroSet
{
    // ...

    public function open(MacroNode $node, PhpWriter $writer): string
    {
        // here we get string of first argument, the file path
        $file = $node->tokenizer->fetchWord();
        if ($file === null) {
            throw new CompileException('Missing SVG file path.');
        }

        // don't forget to clean the quotes
        $svgFilepath = $this->baseDir . DIRECTORY_SEPARATOR . trim($file, '\'"');

        // get optional arguments
        $macroAttributes = $writer->formatArray();

        // print bellow
        // ...
    }
```

The `EmbeddedSvgNode` class splits these jobs into 2 separate methods:

* parse Latte input,
* print itself to compiled PHP templates

<br>

How do we write input parsing of the macro above in the Latte 3 node?

```php
use Latte\Compiler\Nodes\AreaNode;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;

// we chose AreaNode for parent, suitable for HTML nodes like <svg>
final class EmbeddedSvgNode extends AreaNode
{
    private ArrayNode $arguments;

    private string $svgFilepath;

    public function __construct(Tag $tag, string $baseDir)
    {
        // parse first argument
        $stringNode = $tag->parser->parseUnquotedStringOrExpression();
        if (! $stringNode instanceof StringNode) {
            // we need string node
            throw new InvalidArgumentException('File must be string value');
        }

        // fullpath to directory
        $this->svgFilepath = $baseDir . DIRECTORY_SEPARATOR . $stringNode->value;

        // parse optional arguments, after ","
        $tag->parser->stream->tryConsume(',');
        $this->arguments = $tag->parser->parseArguments();
    }
}
```

<br>

## What has Changed in the Input?

* instead of `string` for file name, we get a `StringNode` object
* instead of `string` for arguments (yes, [it's a `string`](https://github.com/nette/latte/blob/f2e16d3ec6968854029740452c20c38a514e6842/src/Latte/Compiler/PhpWriter.php#L164)), we have an `ArrayNode` object

Beautiful object API with IDE autocomplete and static validation!

<br>

Back to our ~~macro~~ node. Now we have 2 essential pieces ready:

* the SVG file path
* the optional arguments, e.g. "class" => "blue"

<br>

The last step is **to print them to compiled PHP code**.

<br>

## 4. From `$writer->write(...)` To `$node->print(...)`

For the printing, we re-use the original macro. What should we print?

```php
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class EmbeddedSvgMacro extends MacroSet
{
    // ...

    public function open(MacroNode $node, PhpWriter $writer): string
    {
        // ...

        // contains ['width' => '10', 'height' => '10']
        $svgAttributes = $this->resolveSvgAttributes($svgFilepath);

        // contains <circle cx="4.5" cy="4.5" r="3.5"/>
        $innerSvgContent = $this->resolveSvgString($svgFilepath);

        return $writer->write('
            echo "<svg";
            foreach (%0.var + %1.raw as $key => $value) {
                echo " " . %escape($key) . "=\"" . %escape($value) . "\"";
         };
         echo ">" . %2.var . "</svg>";
         ',
            $svgAttributes,
            $macroAttributes,
            $innerSvgContent
        );
    }
}
```

<br>

It seems we have to:

* foreach svg attributes, in bare `array` format via `%0.var%` mask
* macro defined arguments in `string` format via `%1.raw` mask
* print inner SVG `string` via `%2.var`

<br>

How do we include it in `EmbeddedSvgNode::print()` method?

```php
use Latte\Compiler\Nodes\AreaNode;
use Latte\Compiler\Nodes\TextNode;
use Latte\Compiler\PrintContext;

final class EmbeddedSvgNode extends AreaNode
{
    // ...

    public function print(PrintContext $context): string
    {
        // contains ['width' => '10', 'height' => '10']
        $svgAttributes = $this->resolveSvgAttributes($svgFilepath);

        // contains <circle cx="4.5" cy="4.5" r="3.5"/>
        $innerSvgContent = $this->resolveSvgString($svgFilepath);

        return $context->format(
            <<<'PHP_CONTENT'
echo '<svg';

foreach (%dump + %node as $key => $value) {
    echo ' ' . %escape($key) . "='" . %escape($value) . "'";
}

%node
echo '</svg>';
PHP_CONTENT,
            $svgAttributes,
            $this->arguments,
            new TextNode($innerSvgContent)
        );
    }
```

<br>

Tip: open this post in the 2nd tab and compare the old Latte 2 macro on the left side and the new Latte 3 node on the right side.

<br>

### What has Changed in Printing?

```diff
-* foreach SVG attributes, in bare `array` format via `%0.var%` mask
```
* print raw `array` via `%dump` mask

<br>

```diff
-* + macro defined arguments in `string` format via `%1.raw` mask
```

* print latte defined argument `ArrayNode` via `%node` mask

<br>

```diff
-* print inner SVG `string` via `%2.var`
```

* print `TextNode` via `%node` mask


<br>

**Proof over promise?** Find an open-source version of this upgrade in [this pull-request](https://github.com/TomasVotruba/embedded-svg/pull/4/files), including tests and detailed parsing SVG via XML.

<br>

Happy coding!
