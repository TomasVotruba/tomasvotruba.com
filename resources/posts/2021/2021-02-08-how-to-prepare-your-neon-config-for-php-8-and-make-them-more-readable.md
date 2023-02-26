---
id: 301
title: "How to Prepare your Neon Configs for PHP 8 and Make them More Readable"
perex: |
    Good coding habits share a single treat. **They all are ~~resistant~~ fluid to future changes**. You don't have to change them when new packages or PHP is released. One of them is explicit code.

    Do you use explicit NEON config syntax? Then upgrade to PHP 8, including deprecations, will not touch you.
    Do you use magic syntax sugar cream? Today we'll look at how to make it right.
---

"The OP neurons aligned in OD pattern suggest better space orientation empowered to MDS usage."

What is explicit text? A text that does not require Googling acronyms to understand it.

<br>

What is **explicit code**? A code that does not require documentation to understand.

## Have you Met... Magic?

<img src="https://imgs.xkcd.com/comics/tar.png" class="mt-4 mb-5">

Could you guess what this line is? No Googling allowed! :)

```yaml
services:
    - Aws\Client(['region'='western'])
```

- A factory or a service?
- Array of arguments or a single argument with an array?

<br>

What about this one?

```yaml
services:
    -
        factory: App\Some\Factory([KEY=%value%])
```

- The array is an argument for the constructor of a factory?
- Or parameters to the factory method?
- The factory method... hm, probably `create()`?
- Is the `KEY` an env variable?
- Is `%value%` a parameter?

<br>

Apart WTF readability and principle of highest surprise, there is a new problem since PHP 8.

## PHP 8 - Named Arguments and Optional Parameter Last

This code used to be valid in PHP 7.4:

```php
function foo($optional = null, $require)
{
}
```

Since PHP 8, it is deprecated (see [3v4l.org](https://3v4l.org/PRo9Y)).

The optional parameters [must be **after required** parameters](https://php.watch/versions/8.0/deprecate-required-param-after-optional), like this:

```diff
-function foo($optional = null, $require)
+function foo($require, $optional = null)
 {
 }
```

Don't worry about PHP code, Rector got [this case covered](https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#optionalparametersafterrequiredrector). But what about all the NEON configs? If we **change PHP code, the configs will break** because they depend on the parameter order.

What about these "named arguments"? That might save us, but first, we have to untie this NEON spaghetti knot.

## 1. Unwrap Inlined Services

First, we need to untie these class + array single lines:

```diff
 services:
-    - Aws\Client(['region'='western'])
+    -
+        class: Aws\Client
+        arguments:
+           - ['region'='western']
```

## 2. Use Named Arguments

Sometimes we have to look at the class constructor to be able to transform NEON correctly:

```php
namespace Aws;

final class Client
{
    public function __construct(
        private array $configuration = [],
    ) {
        // ...
    }
}
```

We see the argument is an `array` and is named `$configuration`. Let's use "named argument" in our NEON too:

```diff
 services:
     -
         class: Aws\Client
         arguments:
-           - ['region'='western']
+           configuration: ['region'='western']
```

Now, what happens if we add a new **required argument** to the constructor? But we can't add it after the optional argument in PHP 8. It will result in the argument order change!

```diff
 namespace Aws;

 final class Client
 {
     public function __construct(
+        private SomeDependency $someDependency,
         private array $configuration = [],
     ) {
         // ...
     }
 }
```

The code will still work thanks to named arguments ✅

## 3. Unwrap Inlined Arrays

Inlined arrays are confusing the same way the inlined services are. Let's get rid of them too while we're at it:

```diff
 services:
     -
         class: Aws\Client
         arguments:
-            configuration: ['region'='western']
+            configuration:
+                region: 'western'
```

There is also `{}` syntax for composed arrays. We can improve it too:

```diff
 services:
     -
         class: App\SocialLogin
         arguments:
-            - { facebook: no, google: yes, twitter: no }
+            params:
+                facebook: no
+                google: yes
+                twitter: no
```

## 4. Remove Implicit Arguments

Do you use a `...` syntax? That is a placeholder for "complete autowired service here":

```yaml
services:
    -
         class: App\Search
         arguments:
             - ...
             - ...
             - 'keys'
```

With named arguments, this is no longer needed:

```diff
 services:
     -
          class: App\Search
          arguments:
-             - ...
-             - ...
-             - 'keys'
+             type: 'keys'
```

Also, the config became much more readable. Now we know that `'keys'` was used for a `type`.

<br>

Although named arguments were in NEON for a while, now our codebase is **more consistent**. The PHP with native named arguments and deprecated optional arguments first make the code base **more robust**.

The same rule that applies in NEON now applies in PHP. One **less "cool" syntax sugar cream** to learn and **more space to focus on what matters to you**.

<br>

Happy coding!
