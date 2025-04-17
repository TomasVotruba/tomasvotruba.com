---
id: 432
title: "How we Maintain Dozens of Symfony Workflows with Ease"
perex: |
    There are dozens of posts and talks about how Symfony Workflows work and what are they for. To give you simplest example, if you have a post - it can be draft, reviewed or published. Workflow components makes sure the transitions between these states are valid. That's it.

    Yet, there is not a single post about how terrible the configuration is.

    On one hand we have Symfony components, like Event Dispatcher and Console, with native PHP 8 strict attributes. On other hand we have **Workflows configuration with fractal array of strings**. It's like a mine field for a developer who's tasked to add a new line there.

    How can we do it better?
---

*Disclaimer: goal is this post is not only to give a solution to a problem. But also to show a way to think about problems, that have easy but wrong solutions. How to find a better way, if there is none in Google nor GPT. If we feel there is a better way, we have to find it ourselves. With our brains and skills. Have fun!*

<br>

Maybe there is a better way than YAML trees, so I asked you first:

<br>

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">I have a question to <a href="https://twitter.com/symfony?ref_src=twsrc%5Etfw">@symfony</a> devs who use workflows extensively.<br><br>How do you manage workflow definitions to be readable and typo-proof? <br><br>I&#39;ve seen 700+ lines long definitions and I&#39;m scared to even look at it. We use PHP already.<br><br>These long array of strings are error-prone,… <a href="https://t.co/dNu88gMwRm">pic.twitter.com/dNu88gMwRm</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1903854772299473355?ref_src=twsrc%5Etfw">March 23, 2025</a></blockquote>

<br>

You've shared couple new syntaxes I didn't know about!

<br>

This is summary of replies:

* use PHP configs - [yes, please](/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)!
* there is [Workflow api in Symfony 5.3 fluent configs](https://symfony.com/doc/current/workflow.html) - good start... **but**!
* custom solution with place/transition interfaces etc. - only moving complexity to another place

<br>

Also, couple replies like:

<em>"This is cool. I always use yaml and I am always extra careful when defining the workflow"</em>

Framework should not add cost extra attention while working with it. Quite the contrary: it should improve our work,  save us attention, so we can be careful about work we love do to. That's why it's called "framework" and not "longitudinal academic research study" :)

<br>

Okay, we have some input and new resources to explore.. What now?

## What is our goal?

* Simple syntax
* One place
* Error-proof as much as possible
* As little writing as possible
* API that leads our hand - syntax tells us what is required and what is optional

<br>

## The Blind Paths

### 1. Something like EventSubscriberInterface

At first, I followed analogy with `EventSubscriberInterface` contract: we implement single interface, it requires single method that returns events. Simple, single place, no config, all in clean PHP. After kicking of, I've hit **2 dead-ends**:

* The interface for event subscriber requirest only 1 type of data - an event name. If we add single event name, it's valid.
* But the workflow requrest multiple data of different structure: name, type ("workflow" or "state_machine"), initial state, places, transitions etc. In a couple minutes, the contract had 10 methods. Moreover, some of them were requried and some of them not. **Same complexity, just in a different place**.

<br>

* The second dead end is the way workflows are loaded in Symfony framework. The `EventDispatcher` service loads services that implement `EventSubscriberInterface`, and works with them in internally (more or less).
* The workflows loading is much more strict - it requires our array mess syntax on the input, then [`FrameworkExtension`](https://github.com/symfony/framework-bundle/blob/c1c6ee8946491b698b067df2258e07918c25da02/DependencyInjection/FrameworkExtension.php#L970-L1089) comes in and creates an `Workflow\Definition` object in ~120 lines. There is no "WorkflowManager" that would accept an external service. If only this building process would be decoupled from extension + compiler pass.

<br>

Let's try one of suggested solutions.

<br>

### 2. Symfony 5.3 fluent config

We're using using [`FrameworkConfig`](https://symfony.com/blog/new-in-symfony-5-3-config-builder-classes) and similar to get [PHPStan deprecation errors early](https://getrector.com/blog/modernize-symfony-configs#content-3-harvest-full-power-of-static-analysis).
But I didn't realize, the workflows has more deeper fluent syntax:

```php
return static function (FrameworkConfig $framework): void {
    $blogPublishing = $framework->workflows()->workflows('blog_publishing');

    $blogPublishing->type('workflow') // or 'state_machine'
        ->supports([BlogPost::class])
        ->initialMarking(['draft']);

    $blogPublishing->transition()
        ->name('to_review')
            ->from(['draft'])
            ->to(['reviewed']);
```

Wow! I wanted to jump into this syntax right away with our hundreds of lines. But, after asking GPT to rewrite first array to this fluent API, I slowly had to face **3 problems**:

* It's again, the **same complexity with different syntax**. The fluent builder is not suitable here, because we have to know what method to call after `->name()`. The API is not leading us, we still have to learn the documentation.
* We don't know what is required, an what is optional. We have to trial and error, run our app and see exception (if any).
* It requires at least Symfony 5.3 + [enabled config generator](https://github.com/rectorphp/swiss-knife/#9-generate-symfony-53-configs-builders), so PHPStan can see them. More complexity, just to add simple config, doesn't feel right.

<br>

## Fluent API vs Nested Fluent API

Fluent APIs excel [at IDE autocomplete](https://getrector.com/blog/rector-1-0-is-here#content-zen-config-with-autocomplete). But once we do conditional nesting, it looses its power.

* We have to know which method to nest and which not.
* Some method return `self` and some return nested object. And some both.
* Last but not least, nested fluent APIs are hell of complexity for IDE and static analysis.

The configs will quickly become like a target on shooting range:

```php
$blogPublishing->transition()->name('to_review')->from(['draft'])->to(['reviewed'])
    ->transition()->name('to_publish')->from(['reviewed'])->to(['published'])
    ->transition()->name('to_reject')->from(['reviewed'])->to(['draft']);
```

<br>

<blockquote class="blockquote text-center mt-5 mb-5">
"I have not failed.<br>
I've just found 10,000 ways that won't work."

<small>Thomas Edison</small>
</blockquote>


Research and experiments haven't yield any progress so far. So I took a step back, a took week break from this topic.


## Reflection

"This might be a touch one", I thought to myself. There are many tempting, same-complexity different-place solutions that would be accepted by our team. But it would make the future maintenance harder, because we'd have to learn different syntax. My mission is to improve the project maintenance, even if we and all current are gone.

## Smoke Safety First

Then I've realized, "hm, how do I want to make such a huge change (1000 lines) without having a safe-guard? Maybe my brain is not ready, because it cannot think freely". We put smoke test on [every single part of project we modify](/blog/cost-effective-container-smoke-tests-every-symfony-project-must-have) - routes, controller, services, event subscribers, entity mapping, test fixtures etc. These test can be used for years and they smoke check important portion of the project.

But we don't have any such "they still load the same way" test for workflows. Time to fix that!

<br>

**How do we test that all workflows still stayed the same?** Well, we test routes by dumping them to `json`, then we verify the json is still the same.

Can we somehow dump the workflow definitions? [We can](https://symfony.com/doc/current/workflow/dumping-workflows.html).

* We get all workflow definitions object from the container.
* We dump them using only text-based dumper - `MermaidDumper`
* We compare the hash of dumped file is still the same.

In short:

```php
$mermaidDumper = new MermaidDumper(MermaidDumper::TRANSITION_TYPE_STATEMACHINE);

$dumpedContents = '';
foreach ($workflows as [$workflow, $supportStrategy]) {
    $dumpedContents .= $mermaidDumper->dump($workflow->getDefinition());
}

$this->assertSame('expectedHash', sha1($dumpedContents));
```

In full, here is the full [test we use in gist](https://gist.github.com/TomasVotruba/4e2a0aabed649346e17ed743b24a6cca).

This simple test saved me a couple times from confidently crashing production. We had valid workflow syntax, yet, but I actually changed the intended behavior to wrong one. No more.


<blockquote class="blockquote text-center">
"The secret of life is to fall seven times<br>
and to get up eight times."
</blockquote>

<br>

## Reach the Goal with Blend

Week has passed, emotions are gone, but mission is not done. Time to get back to work.

Let's take it step by step. We'll definitely use PHP config - a standalone file apart from `framework.php` for better readability:

```php
// app/config/workflows.php
return function (ContainerConfigurator $containerConfigurator): void {
    $workflows = [
        // ...
    ];

    $containerConfigurator->extension('framework', [
        'workflows' => $workflows,
    ]);
};
```

The 2 solutions we've already tried had one problem in common: **what is required and what is optional**?

How do we define required values? We ask for them in a constructor - let's create custom `WorkflowDefinition` object that will hold all relevant configuration:

```php
final class WorkflowBuilder
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly array|string $supports,
        private readonly string $initialMarking,
        private readonly array $markingStore
    ) {
    }
}
```

That's easy. But what about places and **transitions**? Well, all places are already defined in transitions, right? So there is no point in duplicating them.

All we have to add are **transitions** then:

```php
public function addTransition(string $name, array $from, array $to): self
{
    // ...
}
```

We can also use static `create()` method and default values for convenience:

```php
// app/config/workflows.php
return function (ContainerConfigurator $containerConfigurator): void {
    $workflows = [
        WorkflowDefinition::create('post_publishing', 'draft', Post::class, 'state')
            ->addTransition('to_publish', from: 'reviewed', to: 'published');
    ];

    $containerConfigurator->extension('framework', [
        'workflows' => $workflows,
    ]);
};
```

<br>

To improve our experience in case of error, we add validation to `create` method. E.g. when we put accidentally `Past` object or non-existing `status` property, the exception will tell us before config it even reaches framework extension:

```php
use Webmozart\Assert\Assert;

final class WorkflowBuilder
{
    public static function create(
        string $name,
        string $initialMarking,
        string|array $supports,
        string $markingProperty,

        // to make init simple
        string $type = WorkflowType::STATE_MACHINE,
    ): self {
        $markingStore = [
            'type' => 'method',
            'property' => $markingProperty,
        ];

        Assert::notEmpty($supports);
        if (! is_array($supports)) {
            $supports = [$supports];
        }

        foreach ($supports as $support) {
            Assert::classExists($support);
        }

        return new self($name, $initialMarking, $supports, $type, $markingStore);
    }

    // ...
}
```

That's it, we have the created workflow definition with all required items and have nice and clean object API.
Places are derived from the transitions. Nice! There is just **one little problem**: the workflows configuration only accepts dumb arrays:

```php
$containerConfigurator->extension('framework', [
    // ...

    'workflows' => $workflows,
]);
```

And also, we miss validation to check if there is at least 1 transition. Hm, how do we solve this?

