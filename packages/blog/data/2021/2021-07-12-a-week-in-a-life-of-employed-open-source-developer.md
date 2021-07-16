---
id: 328
title: "A Week in a Life of Employed Open&#8209;Source&nbsp;Developer"
perex: |
    Would you contribute an open-source project if it would help your private one? How do you measure such cost-effectiveness? Maybe it's better to fork the package to your own or create your single local service. But who will maintain it and write a test for it? Who will upgrade it when PHP 8 is in EOL?
    <br><br>
    I want to share my point of view on prisoners' dilemma, which you maybe know from your daily work.

tweet: "New Post on the 🐘 blog: A Week in a Life of Employed Open-Source Developer"
---

We are almost finished with upgrade to PHP 8. We already [migrated our custom](/blog/how-to-refactor-custom-doctrine-annotations-to-attributes) `@annotatoins` classes to `#[attributes]` classes and upgrade whole code base to new PHP 8 syntax.

**Why do we want PHP 8 attributes** instead of annotations everywhere?

* strict autocomplete
* single united syntax
* no place for docblock `{("key"='s',),}}` bugs due to a bit of typo here and there
* full native support of PHPStorm autocomplete, PHPStan custom rules, and Rector automated upgrades

But you know what they say:

<blockquote class="blockquote text-center">
    "Upgrading your code is a piece of cake,<br>
    the Devil is in the vendor."
</blockquote>

## The Devil of Upgrade

I think you knew this from your project. You've added a feature or changed behavior. Everything works. Just this tiny file has one bug that you need to solve. Too bad it has `/vendor` in its absolute path.

Our code now looks like this:

```php
final class SomeController
{
    /**
     * @ExternalAnnotation
     */
    #[OurAttribute]
    public function __invoke()
    {
        // ...
    }
}
```

What do you think about when you see this code?

<br>

- Does the annotation and attribute work in inverse order?

```diff
-    /**
-     * @ExternalAnnotation
-     */
     #[OurAttribute]
+    /**
+     * @ExternalAnnotation
+     */
     public function __invoke()
```

<br>

- What if we have annotation and attribute of the same name?

```php
    /**
     * @OurAttribute
     */
    #[OurAttribute]
    public function __invoke()
```

<br>

* How does the annotation parser handles these conflicts?
* What happens when we want to get just one item of `OurAttribute`?
* ...

## 2 Ways to Do 1 Thing?

These questions seem like a decent topic to think about in the evening over a glass of smoked whiskey... But **they're rather a sign of design smell in our architecture**. There should exactly one way to add metadata above methods, [no exception](/blog/how-exception-to-the-convention-does-more-harm-than-good) allowed.

What happens if there is an exception and we have at least two ways to add metadata? Cognitive dissonance kicks in, and we can use whatever we want anywhere we want:

```php
/**
 * @OurAttribute
 */
public function __invoke()

// another class

#[OurAttribute]
public function __invoke()
```

Too bad our project only supports attributes now, and `@OurAttribute` is silently ignored. It will cause the server down on Saturday morning and make a couple of families very happy about having their developer half work a whole weekend to figure this out.

## Back to Vendor

Now that we've made clear attributes are the only way to go:

```php
#[ExternalAnnotation]
#[OurAttribute]
public function __invoke()
```

First, we have to make sure the `@ExternalAnnotation` supports attribute syntax. The PHP 8 is out for 8 months now, so someone else must have already done it. After all, it's [as simple as](/blog/how-to-refactor-custom-doctrine-annotations-to-attributes) adding an `#[Attribute]` above [each annotation](/blog/doctrine-annotations-and-attributes-living-together-in-peace), right?

<blockquote class="blockquote text-center">
I expect that the whole open-source world<br>
is here for me only to solve my personal problems.
</blockquote>

I've checked the [apitte/core](https://github.com/apitte/core/) package we used for annotations and found out. It does not support attributes. Hm, what now? There [is an issue](https://github.com/apitte/core/issues/151) from January 2021, but that's it. I've decided to contribute back to the package developers and maintainers... and clicked on the 👍 button under the issue.

## It's ~~Never~~ so Easy

Oh no, life is not aligned with my wishful thinking again. What now? Maybe it's time to give up and teach my teammates a new exception they [only have to remember](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/): "The A, B, and C are always attributes and D, E, and F are always annotations."

I liked this option initially, but then thought of **being paid for adding my coworkers more meta-work**, making them logarithmically more expensive, which seemed a bit embarrassing for me.

What other options do I have?

Maybe I could add this feature to the `apitte/core` package? Hm, I haven't worked with Nette open-source for the past 5 years, so that it will require some temporary learning - the worst use of my brain cells if you ask me. Well, if it's adding an `#[Attribute]` above each class, I think I can handle it.

This is how the pull request looks in my head:

```diff
 /**
  * @Annotation
  */
+#[Attribute]
 class SomeClass
 {
 }
```

I'm daydreaming about the fame and glory I will receive from the PHP community just for the 10&nbsp;minutes of my work. Let's do this!

I fork the package, create a pull request, learn how to run the `make X` command, and send the pull request. I'm thinking, hm, maybe I should **add a test** to guarantee my contribution works and make the maintainer more open to merge it. I don't want to bother them twice just to ask me for a test.

My plan is simple: copy-paste existing test, duplicate controller fixture class, replace annotations with attributes, and expected the same result as annotations have. That sounds simple enough, right?

## Can Doctrine Annotations Reader handle Attributes?

I made this plan happen and run the test. Nothing. It seems no attribute classes were found. What is wrong? Everything is 1:1. Just instead of `@SomeClass`, there is `#[SomeClass]` - this must work! I run just a single test for a single attribute, only to confirm the previously failed result.

I do a bit of reflecting on a toilet and have an epiphany - there is another vendor package that handles metadata reading.  The `Doctrine\Common\Annotations\AnnotationReader` service!

I'm thinking... the Doctrine annotations are dualistic - there is one class for `Entity`, you don't have to change anything:

```diff
 use Doctrine\ORM\Mapping\Entity;

-/**
- * @Entity
- */
+#[Entity]
class Post
{
}
```

The annotation reader follows the logic. Maybe it has a different name, but that it:

```diff
-Doctrine\Common\Annotations\AnnotationReader
+Doctrine\Common\Annotations\AnnotationAndAttributeReader
```

Well, unless it isn't. After a few minutes of testing, I accepted that the AnnotationReader only reads annotations, not attributes. The `doctrine/annotations` packages do not support any custom reader for attributes. Why does `doctrine/orm` supports dualistic annotations in a single class and `doctrine/annotations` does not? Probably a result of missing Doctrine vendor monorepo.

This [logical exception caused me](/blog/how-exception-to-the-convention-does-more-harm-than-good) **lose my sanity**. Almost, but instead, I've decided to look for some reader package that would understand both annotations. There is some advanced reader in `doctrine/orm`, but we don't use Doctrine and Symfony, so pulling so many new packages just to read simple attributes was not an option.

## A Package that Reads both Annotations and Attributes

After a bit of googling on packagist, I've found [Koriym.Attributes](/blog/doctrine-annotations-and-attributes-living-together-in-peace) package. I hope the name is not as precise as `doctrine/annotations' This package can read annotations.

**My expectations are** that I install this package to `apitte/core`, replace the reader with this one; it will work for both annotations and attributes. I love the naivety of my younger self, but we still got some time to get there.

I've replaced the package and run the tests. Nothing. What is going on? Is the `koriym/Koriym.Attributes` package broken? I check the tests of the package, and it looks fine. This use case should work. There is one more surprise lurking...

How do you think an annotation class looks like for this use case?

```php
/**
 * @Route("some-path")
 */
```

My expectation is:

```php
/**
 * @annotation
 */
class Route
{
    public function __construct(string $path)
    {
    }
}
```

In [reality](https://github.com/symfony/symfony/blob/5.4/src/Symfony/Component/Routing/Annotation/Route.php#L49-L50), it's closer to:

```php
/**
 * @annotation
 */
class Route
{
    public function __construct(array $data)
    {
        if (...) {
            // ...
            $this->path = $data['path'];
        }
    }
}
```

Hmm, so how can I make dualistic annotation/attribute in a single class? **Attributes must use named arguments** to make the most out of them:

```php
/**
 * @annotation
 */
#[Attribute]
class Route
{
    public function __construct(string $path)
    {
        // ...
    }
}
```

Do you know, there are **3 ways to create an annotation class**. But only one of them is compatible with attributes and only supported since the `doctrine/annotations` version was released this summer. Which one is it, and how to make it happen? You can find out in [Doctrine Annotations and Attributes Living Together in Peace](/blog/doctrine-annotations-and-attributes-living-together-in-peace).

## Refactor Everything

I realize how this used to be so simple in my head before I knew anything about this topic... I guess that's what stupidity is.

I have to refactor every single annotation class. Not just their constructor, but also the logic they use inside. We have 2 pull requests to `apitte/core` now:

* one that adds annotation + attribute reader - new feature with a possible change of behavior - https://github.com/apitte/core/pull/161
* one that makes annotation compatible with attributes - no new feature, but the extension of possible future use - https://github.com/apitte/core/pull/162

**The latter PR is pre-step**, so using low-hanging fruit of least surprise, I've pushed it to be merged first. If you can, always use these pre-step PRs to make the feature PR much smaller and easier to review.

## How to Test Pull-Request Before Merge?

Now we have two pull-request with code changes. Tests are passing, PHPStan is passing, coding standards are passing... that should be enough, right? There is one more important test - in our actual project.

Usually, we have to wait for the feature to be merged and tagged before using it.

Are you highly impatient like me? Then you can require a merged dev version:

```json
{
    "require": {
       "apitte/core": "dev-master"
    }
}
```

But what if there are some bugs in the pull request? Can we wait till the PR is merged to the main branch? No. We want to try them right now. Luckily, there is an easter egg in the composer that makes this simpler. You probably know we can require a specific hash of `dev-master:

<img src="https://user-images.githubusercontent.com/924196/125828158-e0efd58a-0243-4d3e-8e93-ff007db9fd48.png" class="img-thumbnail mb-5 mt-5">

Then use this hash next as a version in `composer.lock`:

```json
{
    "require": {
       "apitte/core": "dev-master#d38ga8gb"
    }
}
```

**This versioning is much-preferred** to dynamic `dev-master, that always take the latest version. The latest version can change in time, but the hash `dev-master#d38ga8gb` version always points to the exact commit. It's like a tag for poor people.

## Composer Easter Egg

So how is using `dev-master` in specific hash useful? Now we'll find the easter egg:

<img src="https://user-images.githubusercontent.com/924196/125828932-0792189c-3082-4b94-9568-a92e1eeff936.png" class="img-thumbnail mb-5 mt-5">

**We can use hash in pull-request the same way with `dev-master**:

```json
{
    "require": {
       "apitte/core": "dev-master#1025287"
    }
}
```

The composer will install the exact commit from the pull request!

<br>

This testing found at least five bugs that we would discover only after merging the pull request!

## One Week Later

The `appetite/core` annotations and attributes now work in the `dev-master`. I hope Milan will tag the next version soon to remove `dev-main` from our composer.

Good, now we have the tools ready, `apitte/core` is up to date and ready. I can finally start the refactoring that seemed "so easy" a few days before. The **safest refactoring is one class at a time**, try it, test it and then merge it. If everything works, we can refactor 2 more classes and so on.

This way, the first pull request had a bunch of annotations and attributes above the same method:

```php
/**
 * @ExternalAnnotation
 */
#[ExternalAttribute]
public function __invoke()
{
}
```

Technically this should work, at least by **my expectations**. Too bad the CI disagreed with me.

## 1 Package = 1 Responsibility

I debug the `apitte/core` and then `koriym/Koriym.Attributes` reader a bit. It seems the reader only returns attributes, no annotations. I assumed that it reads both annotations and attributes **at the same time** together. But the tool is a bit different.

* It read the attributes first - if found, it returned them.
* If not, it read the annotations and returned them.

I first fixed this by making a custom reader class in `apitte/core`. A few hours later, I realized this might be changed directly in `koriym/Koriym.Attributes`. I tried to avoid it, as it requires learning the practices and conventions of yet another package.

**But it is the right way to go**, to keep one responsibility per package. `apitte/core` should not care about the reader. Its value is in connecting read annotations or attributes together to resolve API requests.

So I proposed a [pull-request there](https://github.com/koriym/Koriym.Attributes/pull/11). After 3 days of very active feedback from Koriym, the pull request was ready. **Thank you, Koriym, for the attention and time you gave me.**

One risk of contributing to an open-source project is frustrating response times for both parties.

Usually, there is no timetable for getting a response from the maintainer. You don't know if they respond in 10 minutes, today, or in 2 weeks. Sometimes maintainer can respond in a matter of an hour, just to point out the CI is failing. You fix it, push it a matter of 10 minutes. But then you wait 5 more days for another reply. But that's part of the deal, and when it works out, **it's a great unique feeling of compassion across time zones**.

## Meeting My Expectations

We're heading to the end. With two open-source packages, 5 pull-request later, my expectations are met. I'm happy. If I came to this problem today, I would only use the packages without any surprises. I hope this team effort will make this experience smooth for everyone else in the same situation. Thank you, Milan and Koriym, for your teamwork with a narrow focus on what is essential for the package's sake.

<blockquote class="blockquote text-center">
"Contribute the changes to open-source<br>
you want to see in the world."
</blockquote>

## Meet New Friends

Apart from the new features in the 2 packages and our private project, I feel like I had a great time with 2 friends. We were talking about code, but also coding techniques and one's approach to life.

In a code, you can often see more than meets the eye. We can see if the package has clear naming, neat and easy-to-understand README, or a simple design. It's the door to the writer's mind.

So what is the conclusion for today? **If you feel something or someone has failed you, you can always do something about it**. Maybe you improve your life, and maybe you improve the world a little bit too.

<br>

Happy coding!
