---
id: 429
title: "Why AST beats GPTs - featuring php-parser, ChatGPT 4.5 and Grok 3"
perex: |
    As I'm manually writing this article, GPTs are on the hype train now. In this post, we'll use freshly released ChatGPT 4.5 and Grok 3 and see, if they know the AST of PHP well enough to be used on **a large PHP project**.

    Understanding AST takes longer than writing an English sentence in a chat. But once you see the abstract syntax tree in a code you're reading, it cannot be unseen.

    Today I'll try to convince you to start writing your own AST visitors and see how they make magic happen on your codebase.
---

<div class="text-center">
    <img src="https://images.ctfassets.net/m3qyzuwrf176/72iiSqtNQHguLfhMsMtAin/378b425e642b0093007f60db0f4fac8b/The-Matrix-Fill-Still.jpg?fm=webp&w=800" class="img-thumbnail" style="max-width: 35em">
    <br>
    <small class="text-secondary">"I was blind but now I see"</small>
</div>

<br>

## Where do GPTs excel?

Just a couple of days ago, Peter Levels released a HTML + JS flight simulator build with Cursor (IDE-like GPT focused on code).

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">✨ Today I thought what if I ask Cursor to build a flight simulator<br><br>So I asked &quot;make a 3d flying game in browser with skyscrapers&quot; <br><br>And after many questions and comments from me I now have the official [ ✈️ Pieter .com Flight Simulator ] in vanilla HTML and JS<br><br>It&#39;s in my… <a href="https://t.co/pnLqN4Qung">https://t.co/pnLqN4Qung</a> <a href="https://t.co/lJb4FhYNZF">pic.twitter.com/lJb4FhYNZF</a></p>&mdash; @levelsio (@levelsio) <a href="https://twitter.com/levelsio/status/1893350391158292550?ref_src=twsrc%5Etfw">February 22, 2025</a></blockquote>

<br>

He shares a vibe coding session with improvements to the game. Fun to watch, and worth [following](https://x.com/levelsio).

<br>

This shows the main advantages of GPTs:

* **they are probabilistic**
* bootstrap quickly
* iterate until works
* solo-coding
* no need to understand the codebase
* works great in small, mid-range context

<br>

**GPTs are based on probability**, as they learn on existing available data without weights. The more data you have on a specific topic, the easier it for them is to understand and generate it.

<br>

Using HTML and JS is easy, with 10 000+ sources on the topic. But what about Symfony 7.3?

<div class="text-center">
    <img src="/assets/images/posts/2025/ast-gpt/not_released_yet.png" alt="" class="img-thumbnail">
    <br>
    <small class="text-secondary">
        It's not even released yet, but GPTs don't care.
    </small>
</div>

<br>

## Where does AST excel?

An abstract syntax tree is a way we see the code. We use it to look for [precisely defined pattern](https://getrector.com/blog/how-to-strangle-your-project-with-strangle-anti-pattern#content-pattern-refactoring). If the pattern is found, we do some action. If not, we move on.

We can run it on a legacy codebase to upgrade 10 000 Doctrine entity annotations to PHP 8.0 attributes:

```diff
use Doctrine\ORM\Mapping as ORM;

class User
{
-    /**
-     * @ORM\Id
-     * @ORM\Column(type="integer")
-     * @ORM\GeneratedValue(strategy="AUTO")
-     */
+    #[ORM\Id]
+    #[ORM\Column(type: 'integer')]
+    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;
```

This shows the main advantages of AST:

* **it's deterministic** - has only 1 reaction to 1 input
* it excels in the area of defined rules, the stricter the better - e.g. syntax of programming languages
* it works at scale - to change 1 file or 100 files takes a couple of seconds, 10 000 files ~ 2 minutes
* the better you understand the codebase, the more power AST gives you
* **they're rapidly reusable** - once we solve one pattern, every programmer in the world can reuse it instantly

<br>

<div class="text-center">
    <img src="/assets/images/posts/2025/ast-gpt/reusable.png" alt="" class="img-thumbnail">
    <br>
    <small class="text-secondary">
        Once you learn AST for PHP, it's <a href="https://www.reddit.com/r/PHP/comments/wy5c0k/comment/ilwt4xm/">reusable in other languages too</a>.
    </small>
</div>

<br>
<br>

One more advantage is that **abstract syntax tree is a concept**. Like light refraction, dependency injection, cost-benefit effect, etc. Once we learn it, we know it, forever.

<br>

## Innovation Propagation Lag

Do you remember how you wanted to know how to use PHP latest features? We used Google to find a solution, mostly on Stackoverflow. We lacked understanding, so we copy-pasted the code and hoped it would work - in a similar way someone does with GPTs now. There was just one problem, the most popular solutions had high scores in both Google and Stackoverflow rankings. The new and innovative solutions were not there, as they were not popular yet.

That leads to a state where **the most popular solutions were the most outdated ones**. Especially for a language, that releases a new version every year. Yes, we can use a hack to limit all Google/StackOverflow result to last 12 months, but the easiest path always wins.

**GPTs suffer from the same design flaw** - you've probably seen jokes about the ChatGPT cut-off date, as it yields old presidents instead of those elected 2 months ago.

This flaw can be counterbalanced by shortening the cut-off date or just keep learning on fresh data. That's what Grok is trying to do. Yet, the innovation is very slow and **GPTs keep propagating outdated solutions and patterns**, because there are more sources, more discussions, and more strong opinions.

## 6 Years Behind

We humans suffer from the same flaw. We tend to stick with existing solutions that we've known for years, instead of constantly trying new ones.

We have PHP 8.4 out now, but a lot of codebases do not use even old PHP features. One of the most missed features is param/return `string`, `int`, `bool`, and `float` and object type declarations:

```php
function addNumbers(int $a, int $b): int
{
    return $a + $b;
}
```

Too new? They were released in PHP 7.0, in January 2019, that's 6 years ago. Still, many codebases including frameworks are not using them. We're lagging 6 years behind the released feature.

How can we adapt our existing codebases to something 1-year-old then?

<br>

In the context of this post, we should ask: "How can GPTs adapt to something 1 year old and suggest it at first shot"?

<br>

## ~~Legacy~~ Successful Projects

<blockquote class="blockquote text-center">
    "These mountains that you are carrying<br>
    you were only supposed to climb"
</blockquote>

When we talk about legacy projects, we don't include only a mix of PHP and HTML with thousands of files. Legacy projects include existing projects:

* that are at least a couple of years old,
* **they're profitable**
* they pour a portion of the money to "just keep running"
* they could **be growing much faster at lower overhead expenses**

These projects already have value and are growing. Also, they have more value to be extracted. The same way 5-story old buildings in capital cities...

<div class="text-center">
<img src="https://www.tudorbuilding.com.au/img/asset/YXNzZXRzL2Jsb2cvYmxvZy0xLWltYWdlLTIuanBn/blog-1-image-2.jpg?q=75&s=3eb0c46b17b226f4192a1818493846a7" class="img-thumbnail" style="max-width: 35em">
<br>
<small class="text-secondary">
...the same way you can extract a whole new floor in your house.
</small>
</div>

<br>

<br>

Legacy projects don't have a bad carma, but rather **hidden source of great power to be discovered**.

<br>

## How to learn AST?

The best way of learning is by doing something meaningful to you: pick 1 problem in your PHP codebase that you have known about for years and fix it.

* download `nikic/php-parser`
* of course, use GPT to consult
* try [AST interactive dumpers](https://getrector.com/ast) on Rector website
* or read real AST code examples from [Rector book](https://leanpub.com/rector-the-power-of-automated-refactoring) - price just dropped by 40 % this week

<br>

## Use case: Upgrade FOS Rest bundle 2 to 3

For simple upgrades, we can use IDE or in-IDE GPTs. For more complex we can use Rector, which has already prepared sets to handle e.g. PHP, Symfony, Laravel, or PHPUnit upgrades.

But we care about our specific project, which neither GPT nor Rector has heard about enough times.

I'll try to ask ChatGPT 4.5 and Grok 3 to help build custom rules and will review their process, so you can see why the AST and your own creativity and determination beat GPT in the long run.

<br>

Our status: we assume GPTs will help us handle our work. We don't know much about AST yet and want to see, if it's worth learning.

Our task: We have a project with FOS Rest bundle 2 and need to upgrade to version 3, which allows a higher Symfony version.

The challenge: the routing in version 2 is magical and requires only a definition in the YAML file:

```yaml
some_routes:
    type:         rest
    resource:     "@Controller/ProjectsController.php"
    prefix:       /sites
    defaults: { _format: json }
```

The official upgrade guide mentions this challenge [very vaguely](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/3.x/UPGRADING-3.0.md#upgrading-from-2x-to-30), as requires huge amount of manual work. Changing "rest" to "annotation" doesn't help here, because the controller doesn't have any annotations in the first place.

<br>

To kick off, let's ask for help GPTs. We want to create a Rector rule that we would be able to copy-paste to our project, run, and get the job done:

```bash
Create a Rector rule to migrate fosrest 2 routes to fosrest 3 routes on controller
Make use of @Route annotations, take it step by step
and do before/after code samples
```

Instead of posting conversation back and forth to this post, I'll share the full conversation:

* Here is [full ChatGPT 4.5 conversation](https://chatgpt.com/share/67c9ab8c-64c8-800a-b6b9-bbc84888cf8d)
* Here is [full Grok 3 conversation](https://x.com/i/grok/share/NQsjnIElxJVEJxqLlkMIPtzUr)

<br>

## What Problems have GPTs missed?

Both tools have failed to provide a working code. Also, due to innovation propagation lag they're leading us astray in syntax that has been outdated for 2 years now.

I'll comment on the most relevant fails where using GPT will give us more work and force us to research deeper back and forth.
My thesis is that it would be faster to understand AST and write the rule from scratch ourselves, with understanding and full control. This further allows us to improve the Rector rule to catch edge cases used only in our project.

<br>

At first reply, both ChatGPT and Grok assumed I already had annotation routes in the controller:

```php
/**
 * @Rest\Get("/users/{id}", name="get_user")
 */
public function getUser($id)
{
    return $this->json(['id' => $id]);
}
```

This is the happy path of simple annotation rename, so it makes sense GPT went for it. After correction that we use YAML files with definition, it got lost 1st time. So I've added a little bit more context:

```bash
I need a rule that works with route.php file routes like:

$routingConfigurator
    ->import(__DIR__ . '/../src/SomeBundle/Resources/config/routing.yml', 'rest')
    ->prefix('/api/some-prefix');

And convert this to @Route annotations above controller actions



Here is the routing.yml:

some_routes:
    type:         rest
    resource:     "@Controller/ProjectsController.php"
    prefix:       /sites
    defaults: { _format: json }
```

<br>

**ChatGPT 4.5 hallucinated a couple of non-existing services**. They look real but do not exist. Good luck finding those:

* `RoutingConfiguratorImportAnalyzer`
* `SymfonyRouteAnnotationNodeFactory`

<br>

**ChatGPT 4.5** started to create a rule for adding `@Route` annotations correctly, but then it got lost and started to parse the `$routingConfigurator->import` PHP route file. This part is actually the least important and is not used for adding routes.

<br>

**ChatGPT 4.5 hallucinated** `printNodesToFilePath()` method, which does not exist in Rector.

<br>

**Grok 3 rigidly assumed** all the controller actions are named in the same way, e.g. 'getAction'. That means `getProduct` would be skipped.
Also, it missed the `cget*()` prefix, which should be also converted.

<br>

**Both tools are lagging and using Rector 0.9 config syntax**, [without fluent API](https://getrector.com/blog/rector-1-0-is-here#content-zen-config-with-autocomplete). Copy-pasting such syntax would probably lead to bugs while using Rector 2.0, setting your project even further behind.

<br>

They're also **using the wrong namespace** - the `use Rector\Core\*;` no longer exists. The `use Rector\` should be used instead since Rector 1.0. Copy-pasting such code would lead to infamous class-not-found errors Stackoverflow is full of.

<br>

They both miss-understood the separation process:

* 1st we have to collect route data from YAML route files
* 2nd then we can run the Rector rule on `Class_` nodes and use collected data

<br>

## Conclusion

In my opinion, the GPTs fail here for both developer groups.

### Developers who don't know AST

If I was a developer who has little or no AST knowledge, I would get lost as GPT gives them only half-baked cake. Some parts will not work because code is outdated. If after 6 years we're unable to use PHP 7.0 strict type declarations in PHP projects, not sure how many years it will take GPTs to start using Rector 2.0 syntax.

This is the innovation propagation lag in practice. By the time Rector 3.0 is out, GPTs will still be using Rector 2.0 syntax, and so on. It forces us to stop the innovation and go for BC compatibility forever, creating an even more coupled legacy that we actually try to get rid of.

### Medior/Seniors AST devs

More experienced AST developer sees obvious mistakes that GPTs make and tries to fix them. Yet after the first couple of feedback, GPTs already forgot about the controller routes and focused only on YAML configs. It would be definitely faster to write such a rule with the help of Copilot.

<br>

That's why it's worth learning AST because you'll be able to shape codebases reliable with your own hands and vast imagination.

<br>

Happy coding!
