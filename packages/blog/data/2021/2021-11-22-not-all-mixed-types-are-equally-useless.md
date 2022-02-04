---
id: 348
title: "Not all Mixed Types are Equally Useless"
perex: |
    Do you have a big project where you try to raise the PHPStan level as high as possible? Yet, you're stuck on level 4 or 5 with thousands of errors? We all have one and try to chip away a few errors now and then.
    <br><br>
    The `mixed` type is the worst of all of them. Fixing all `mixed` types to a specific type is a nightmare for the REST of your life (pun intended). But what if there are places where fixing `mixed` type **brings much more value than** in the others?

tweet: "New Post on the 🐘 blog: Not all Mixed Types are Equally Useless"
tweet_image: "/assets/images/posts/2021/equal_animals.png"
---

<img src="/assets/images/posts/2021/equal_animals.png" style="max-width: 32em" class="img-thumbnail mt-2 mb-5">

We're in a state where code runs, tests pass, and everything works. We can find snippets like this one:

```php
function printNames(array $names)
{
    foreach ($names as $name)
    {
        echo $name;
    }
}
```

We know that the `$names` parameter is `mixed[]` type. It is probably a `string[]`, `int[]` or `StringableInterface` type. We could run the code, write the test, detect the type and complete it.

But by adding a specific type here, **we will not get much new information**. The value will still be echo-able, and the code will still work. We will have one less ignored error in `phpstan.neon`.

## Business-Focused Low Hanging Fruit

Maybe my boss would let me remove all the `mixed` types from the project, but it would make their expenses high enough to fire me. The coding must be economical and beneficial for the project's future.

Instead of looking for the origin of `mixed` with the attention of detective Colombo, we could do something better with our time. We could find those `mixed` type that is now blocking PHPStan from further analysis. We do not have to test manually a `mixed` type and hope for the user input.

In the same project, we find the following code:

```php
function printNames(array $videos)
{
    foreach ($videos as $video)
    {
        echo $this->renderUrl($video->getUrl());
    }
}
```

From the PHPStan point of view, this snippet is identical to the previous one. There is a variable `$videos` with `mixed[]` type. **PHPStan can't analyze the `mixed` type**, as it can be anything from scalar, `null`, `false`, object, collection of objects and nested array of all above, etc.

What can we assume from this code?

```php
$video->getUrl()
```

* it's a method call
* the `$video` is an object
* the `$video` object has a `getUrl()` method

How can we deduct the type of object here?

## PHPStorm Search to the Rescue

Here you could read a complex passage about types, abstract syntax tree, static analysis, etc.

<br>

Instead, we'll be KISSing PHPStorm:

* use *Find in files* action
* search for a "public function getUrl()" string

<img src="/assets/images/posts/2021/find_in.png" style="max-width: 32em" class="img-thumbnail mt-2 mb-2">

<br>

We see the responsible type is `Video` object, and we can complete it to our code:

```diff
+/**
+ * @param Video[] $videos
+ */
 function printNames(array $videos)
 {
     foreach ($videos as $video)
     {
         echo $this->renderUrl($video->getUrl());
     }
 }
```

Thanks to this type the PHPStan now can analyze if:

* arguments in `getUrl()` are valid (if any provided)
* the `$this->renderUrl()` param type is compatible with `getUrl()` return type

<p class="text-success pt-3 pb-3">
    ✅
</p>

That's how we can use a method call on `mixed` to our advantage.

<br>

## The "Object `mixed`" > any `mixed`

The second use case for object `mixed` is similar. You can probably already guess it:

```php
function collectIds(array $videos): array
{
    $ids = [];
    foreach ($videos as $video)
    {
        $ids[] = $video->id;
    }

    return $ids;
}
```

Yes, it's property fetch. We can fetch property only on a specific object. Well, except `stdClass` that is typical for `json_decode()` return values. But apart from that, property fetch is a sure sign of a known object.

Again, we'll be KISSing PHPStorm:

* use *Find in files* action
* search for a "public $id;" string

<br>

And complete the types based on found `Video` object:

```diff
+/**
+ * @param Video[] $video
+ * @return int[]
+ */
 function collectIds(array $videos): array
 {
     $ids = [];
     foreach ($videos as $video)
     {
         $ids[] = $video->id;
     }

     return $ids;
 }
```

<br>

Our code is now slightly more intelligent, and PHPStan now sees:

* if `$video` has property `$id`
* the `@var`/strict type of `$id` property

<p class="text-success pt-3 pb-3">
    ✅
</p>

## How detect Object `mixed` with PHPStan?

We can teach PHPStan to report these low-hanging fruit cases. The conditions are straightforward:

* the variable must be `Variable` (no magic)
* the variable type must be `mixed`
* the element must be one of ↓

```php
// method call → MethodCall node
$video->getUrl();

$video->id;
// property fetch → PropertyFetch node
```

<br>

You'd be surprised if you'd have to write those rules on your own.

They're freshly included in [symplify/phpstan-rules](https://github.com/symplify/phpstan-rules) ↓

* [NoMixedMethodCallerRule](https://github.com/symplify/symplify/pull/3913)
* [NoMixedPropertyFetcherRule](https://github.com/symplify/symplify/pull/3912)

<br>

How did the rule perform on Symplify itself? The property rule passed without any report, but the method called one **found over 20 cases of unknown type**. Despite the fact we have PHPStan on level 8. Pretty neat, right?

<br>

## How "Equal" is Your Project?

How many `object` mixed types do you have? Register rules and let PHPStan disclose the magic:

```yaml
# phpstan.neon
services:
    -
        class: Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule
        tags: [phpstan.rules.rule]

    -
        class: Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule
        tags: [phpstan.rules.rule]
```

<br>

Happy coding!
