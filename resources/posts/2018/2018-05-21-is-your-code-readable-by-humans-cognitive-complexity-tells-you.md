---
id: 107
title: "Is Your Code Readable By Humans? Cognitive Complexity Tells You"
perex: |
    Cyclomatic complexity is a static analysis measure of how difficult is code to test.
    **Cognitive complexity** tells us, how difficult code is to understand by a reader.

    Today, we'll see why is the later better and how to check it in your code with a Sniff.

updated_since: "March 2023"
updated_message: |
    Use new PHPStan micro-package that deals with cognitive complexity - [TomasVotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity).
---

## What is Cognitive Complexity?

*Tomáš Horváth* referenced me to [Cognitive Complexity, Because Testability != Understandability](https://blog.sonarsource.com/cognitive-complexity-because-testability-understandability) under the [Cyclomatic Complexity](https://pehapkari.cz/blog/2018/04/04/cyklomaticka-komplexita) post. Thank you Tomas.

The most important source about *Cognitive Complexity* is [a 21-page long PDF](https://www.sonarsource.com/docs/CognitiveComplexity.pdf). Instead of explaining in words (you can read that in the PDF), **here are 2 examples that speak more than a thousand words**:

<br>

### Example A

Cyclomatic Complexity: 4

```php
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
```

**vs. Cognitive Complexity: 1**

```php
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
```

### Example B

Cyclomatic Complexity: 4

```php
function sumOfPrimes($max) {            // +1
    $total = 0;
    for ($i = 1; $i < $max; ++$i) {     // +1
        for ($j = 2; $j < $i; ++$j) {   // +1
            if ($i % $j === 0) {        // +1
                continue 2;
            }
        }

        $total += $i;
    }

    return $total;
}
```

**vs. Cognitive Complexity: 7**

```php
function sumOfPrimes($max) {
    $total = 0;
    for ($i = 1; $i < $max; ++$i) {     // +1
        for ($j = 2; $j < $i; ++$j) {   // +2
            if ($i % $j === 0) {        // +3
                continue 2;             // +1
            }
        }

        $total += $i;
    }

    return $total;
}
```

If I should put it in own words, the *cognitive complexity* is **how difficult is to understand
a function and all its possible paths**.

- Example A: **there is a `switch()` and based on `$number` returns a specific value.** Even if there are 50 `case:`, the story is still the same.

- Example B: **there are 3 `for`s, with nesting and one continue to non-self level**.

## Automation Over Information

This all is nice to know information. The one that you might find interesting, remember it for few days and then forget it and never meet it again. But I'm too lazy to *learn to just forget*, so I *learn to automate*. This is place [to write a Sniff](/blog/2017/07/17/how-to-write-custom-sniff-for-code-sniffer-3/).

It took me 5 days to understand academic writings in the PDF, to convert Java and Python examples to PHP and reverse-engineer the algorithm to compute cognitive complexity to match results in the PDF. **The most difficult was to change the cyclomatic complexity approach I used for last 4 years to a human one**.

Today, I'm happy to show you the first version of `CognitiveComplexitySniff`.

## 3 Steps to Check Cognitive Complexity of Your Code

**1. Install package**

```bash
composer require tomas-votruba/cognitive-complexity --dev
```

**2. Configure your `phpstan.neon`**

```yaml
# phpstan.neon
parameters:
    cognitive_complexity:
        class: 50
        function: 8
```

**3. Run PHPStan**

```bash
vendor/bin/phpstan
```

<br>

## Refactor to Lower Cognitive Complexity in Examples

<a href="https://github.com/symplify/symplify/pull/823/files" class="btn btn-dark btn-sm">
    See pull-request
</a>

Saying "refactoring this" is very simple, but actual work and teaching others is a very challenging task. To make this a bit easier for you, I've extracted few refactorings I made in Symplify thanks to this Sniff.

### 1. Refactoring to Shorter Condition

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

### 2. Refactoring with Method Extraction

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

### 3. Refactoring to Responsible Method

```diff
diff --git a/packages/CodingStandard/src/Fixer/Import/ImportNamespacedNameFixer.php b/packages/CodingStandard/src/Fixer/Import/ImportNamespacedNameFixer.php
index 1d532ca58..8aa7981cb 100644
--- a/packages/CodingStandard/src/Fixer/Import/ImportNamespacedNameFixer.php
+++ b/packages/CodingStandard/src/Fixer/Import/ImportNamespacedNameFixer.php
@@ -148,10 +148,6 @@ public function fix(SplFileInfo $file, Tokens $tokens): void
             }

             if ($token->isGivenKind(T_DOC_COMMENT)) {
-                if (! $this->configuration[self::INCLUDE_DOC_BLOCKS_OPTION]) {
-                    continue;
-                }
-
                 $this->processDocCommentToken($index, $tokens);
                 continue;
             }
@@ -274,6 +270,10 @@ private function processStringToken(Token $token, int $index, Tokens $tokens): v

     private function processDocCommentToken(int $index, Tokens $tokens): void
     {
+        if (! $this->configuration[self::INCLUDE_DOC_BLOCKS_OPTION]) {
+            return;
+        }
+
         $phpDocInfo = $this->phpDocInfoFactory->createFrom($tokens[$index]->getContent());
         $phpDocNode = $phpDocInfo->getPhpDocNode();
 ```

<br><br>

Happy Code Reading!
