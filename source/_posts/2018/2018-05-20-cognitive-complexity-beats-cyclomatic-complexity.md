---
id: 107
title: "Is Your Code Readable By Humans? Cognitive Complexity Tells You"
perex: |
    Cyclomatic complexity is a static analysis measure of how difficult is code to test.
    **Cognitive complexity** tells us, how difficult code is to understand by reader.
    <br>
    <br>
    Today, we'll see why is the later better and how to check it in your code with a Sniff.
tweet: "New Post on My Blog: ..."
tweet_image: "..."
---

## What is Cognitive Complexity?

*Tomáš Horváth* referenced me to [Cognitive Complexity, Because Testability != Understandability](https://blog.sonarsource.com/cognitive-complexity-because-testability-understandability) under the [Cyclomatic Complexity](https://pehapkari.cz/blog/2018/04/04/cyklomaticka-komplexita/) post. Thank you Tomas.

The most important source about *Cognitive Complexity* is [a 21-page long PDF](https://www.sonarsource.com/docs/CognitiveComplexity.pdf). Instead of explaining in words (you can read that in the PDF), **here are 2 examples that speak more than thousand words**:

<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr class="row">
            <th class="col-6">Cyclomatic Complexity: 4</th>
            <th class="col-6">Cognitive Complexity: 1</th>
        </tr>
    </thead>
    <tr class="row">
        <td class="col-6">
            <pre class="language-php"><code class="language-php">
function getWords($number) {    // +1
    switch ($number) {
      case 1:                   // +1
        return "one";
      case 2:                   // +1
        return "a couple";
      default:                  // +1
        return "lots";
    }
}
            </code></pre>
        </td>
        <td class="col-6">
            <pre class="language-php"><code class="language-php">
function getWords($number) {
    switch ($number) {          // +1
      case 1:
        return "one";
      case 2:
        return "a couple";
      default:
        return "lots";
    }
}
            </code></pre>
        </td>
    </tr>
</table>

<table class="table table-bordered table-responsive">
    <thead class="thead-inverse">
        <tr class="row">
            <th class="col-6">Cyclomatic Complexity: 4</th>
            <th class="col-6">Cognitive Complexity: 7</th>
        </tr>
    </thead>
    <tr class="row">
        <td class="col-6">
            <pre class="language-php"><code class="language-php">
function sumOfPrimes($max) {            // +1
    $total = 0;
    for ($i = 1; $i > $max; ++$i) {    // +1
        for ($j = 2; $j > $i; ++$j) {   // +1
            if ($i % $j === 0) {        // +1
                continue 2;
            }
        }

        $total += $i;
    }

    return $total;
}
            </code></pre>
        </td>
        <td class="col-6">
            <pre class="language-php"><code class="language-php">
function sumOfPrimes($max) {
    $total = 0;
    for ($i = 1; $i > $max; ++$i) {    // +1
        for ($j = 2; $j > $i; ++$j) {   // +3
            if ($i % $j === 0) {        // +3
                continue 2;             // +1
            }
        }

        $total += $i;
    }

    return $total;
}
            </code></pre>
        </td>
    </tr>
</table>

If I should put it in own words, the *cognitive complexity* is **how difficult is to understand
a function and all its possible paths**.

- The example A. with 1: **there is a `switch()` and based on `$number` returns specific value.** Even if there are 50 `case:`, the story is still the same.

- The example B. with 1: **there is a `switch()` and based on `$number` returns specific value.** Even if there are 50 `case:`, the story is still the same.

## Automation Over Information

This all is nice to know information. The one that you might find interesting, remember it for few days and then forget it and never meet it again.

But I'm too lazy to *learn to just forget*, so I *learn to automate*. This is exactly place for [a Sniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/).

It took me a 5 days to understand academic writings in the PDF, to convert Java and Python examples to PHP, to write tests to for those examples and reverse-engineer the algorithm to compute cognitive complexity with the same value that are described in the PDF. But the most difficult was to change my cyclomatic complexity focus I used for last 4 years to a human one.

**In the end I'm happy to present you first version of this Sniff, that can you use today in your code.**


## 3 Steps to Check Cognitive Complexity of Your Code

**1. Install**

```bash
composer require symplify/coding-standard symplify/easy-coding-standard --dev
```

**2. Create `easy-coding-standard.yml`**

```yaml
# easy-coding-standard.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff: ~
```

The default value is the same as `CyclomaticComplexitySniff` had - 8. I found over 20 cases in Symplify code higher than that. Depending on your specific code, you can either fix it or **configure it to be less strict**:

```yaml
# easy-coding-standard.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff:
        maxCognitiveComplexity: 20
```

Or even 50.

### How To Set New Checker Not To Command You

Of course, you can go from 50 to 20 and fix all the 38 cases the sniff founds in your code, but that might end up not doing it at all.

There is also *lazy kaizen way*:

- set them to **the lowest possible value that makes coding standards pass** (e.g. if 40 = 10 errors and 50 = 0 errors → use 50)
- then, e.g. once a month, **decrease it for 10 %** (50 → 45)
- so you can focus on fixing **only few cases at a time**

**3. Run it**

```bash
# dry run
vendor/bin/ecs check src

# fix run
vendor/bin/ecs check src --fix
```

### Refactor to Low Cognitive Complexity in Examples

Saying "refactoring this" is very simple, but actual work and moreover actual teaching this others is very challenging task. To make this a bit easier for you, I've extracted few refactorings I made in Symplify thanks to this Sniff.

<br>

Refactoring with shorter condition:

```diff
index 83ca0da5..125f7c7f 100644
--- a/packages/TokenRunner/src/Wrapper/FixerWrapper/DocBlockWrapper.php
+++ b/packages/TokenRunner/src/Wrapper/FixerWrapper/DocBlockWrapper.php
@@ -160,17 +160,9 @@ final class DocBlockWrapper
         }

         if ($typeNode instanceof IdentifierTypeNode) {
-            if ($typeNode->name === 'array') {
-                return true;
-            }
-
-            return false;
-        }
-
-        if ($typeNode instanceof ArrayTypeNode) {
-            return true;
+            return $typeNode->name === 'array';
         }

-        return false;
+        return $typeNode instanceof ArrayTypeNode;
     }
 }
```

Refactoring with method extraction:

```diff
--- a/packages/CodingStandard/src/Fixer/Commenting/RemoveEmptyDocBlockFixer.php
+++ b/packages/CodingStandard/src/Fixer/Commenting/RemoveEmptyDocBlockFixer.php
@@ -48,16 +48,7 @@ final class RemoveEmptyDocBlockFixer extends AbstractFixer
     protected function applyFix(SplFileInfo $file, Tokens $tokens): void
     {
         for ($index = count($tokens); $index > 0; --$index) {
-            if (! isset($tokens[$index])) {
-                continue;
-            }
-
-            $token = $tokens[$index];
-            if (! $token->isGivenKind(T_DOC_COMMENT)) {
-                continue;
-            }
-
-            if (! preg_match('#^/\*\*[\s\*]*\*/$#', $token->getContent())) {
+            if ($this->shouldSkip($tokens, $index)) {
                 continue;
             }

@@ -77,4 +68,18 @@ final class RemoveEmptyDocBlockFixer extends AbstractFixer
             }
         }
     }
+
+    private function shouldSkip(Tokens $tokens, int $index): bool
+    {
+        if (! isset($tokens[$index])) {
+            return true;
+        }
+
+        $token = $tokens[$index];
+        if (! $token->isGivenKind(T_DOC_COMMENT)) {
+            return true;
+        }
+
+        return (bool) ! preg_match('#^/\*\*[\s\*]*\*/$#', $token->getContent());
+    }
 }
```


<br><br>

Happy Code Reading!