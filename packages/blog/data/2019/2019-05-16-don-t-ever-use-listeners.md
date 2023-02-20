---
id: 212
title: "Don't Ever use Symfony Listeners"
perex: |
    Another anti-pattern that deserves more attention than it has. I often see this in Symfony projects I consult and when I ask the dev *why* did he or she choose listener over subscriber, they don't really know - "it was in the Symfony documentation, you can read it there".


    Not good enough. **So why you should never use a listener?**
tweet: "New Post on #php 🐘 blog: Don't Ever use #Symfony Listeners"
tweet_image: ""
---

When we look into Symfony [EventDispatcher documentation](https://symfony.com/doc/current/event_dispatcher.html), this is the first YAML code we see:

```yaml
# config/services.yaml
services:
    App\EventListener\ExceptionListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
```

If I'd be in the process of learning Symfony, this would be my thoughts:

- "I see that this option is the first one in the official Symfony documentation"
- "Right above something called *Subscriber*"
- "I guess I should use Listeners by default then. Why? For some unknown reason that only Symfony seniors know."

Btw, there is not even "Subscriber" in the main headline!

<img src="/assets/images/posts/2019/sub/listen_first.png" class="img-thumbnail">

That's how you write a manipulative text if you wanted people to never use subscribers :).

## What's Wrong With Listeners?

- Juniors will use Listeners by default (everywhere they can) ❌
- YAML configs will get fat with listener configuration for basically no advantage ❌
    - Since [PSR-4 autodiscovery](/blog/2018/12/27/how-to-convert-all-your-symfony-service-configs-to-autodiscovery/) this hurts config readability more then ever
    - You have to remember the YAML syntax for right registration
    - Do you know you have to tag it with `name` & `event`?
    - Will `_autoconfigure: true` help you here?
    - What's the name of event - `kernel_exception` or `kernel.error`? Well, neither
- What will you do if name of Kernel event will change? ❌
- How do you analyse it with PHPStan? ❌
- How do you upgrade it with Rector, when Symfony will create a [BC break](https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching) [change](/blog/2020/05/25/the-bulletproof-event-naming-for-symfony-event-dispatcher/)? ❌
- What if you decide to migrate to Laravel or the *new best framework X* later? ❌

All these problems will shoot you or your colleague in the back in the future. **You've just opened doors for 6 more possible bugs and problems** to come to your project #carpeyolodiem.

Most of these problems are a result of config programming - [that just sucks](/blog/2019/02/14/why-config-coding-sucks/).

## Why You Should Always use Event Subscriber?

Listeners have only one valid use case - it's a 3rd party code located in your `/vendor` and someone else wants you to use it with event of your choice in config, e.g.:

```yaml
# config/services.yaml
services:
    Vendor\ThirdPartyProject\Listener\UseMeListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
            - { name: kernel.event_listener, event: kernel.view }
```

<br>

If it would be a subscriber, it would be very similar to this:

```php
<?php

namespace App;

use Vendor\ThirdPartyProject\Listener\UseMeListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class YourListener extends UseMeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
            KernelEvents::VIEW => ['onKernelView'],
        ];
    }
}
```

What's wrong with this code? First, `UseMeListener` should be [`final`](/blog/2019/01/24/how-to-kill-parents/), so you cannot break SOLID like this.

Let's take part by part.

### 1. Validated PHP over Config Typos

```php
$myEvents = [
    KernelEvents::EXCEPTION => ['onKernelException'],
    KernelEvents::VIEW => ['onKernelView'],
];
```

- Using `KernelEvents::EXCEPTION` constant is **a big win**! Instead of some string a config that we cannot analyze nor refactor, we have a constant. If you create a typo like `KernelEvents::EXCEPTON`, you'll know. If you make a typo in a config? Good luck!

- How is `'onKernelView'` string made? I have no idea. It's a convention name, that is somehow resolved from tag name in the config to a protected/public? local method that is called. We don't need that magic, right Mr. Potter?

### 2. Explicit Services instead of Circular-Coupled Subscriber/Listener

If someone has created a listener that you can re-use, it's an anti-pattern already.

- Would you create a controller, that someone should call in another controller?
- Or a command, that someone should use in their listener or controller?

People actually do that, the [StackOverflow has dozens of questions like "how to call command in a controller" or "how to controller in a command"](https://stackoverflow.com/questions/31512200/calling-action-from-command).

### 3. Don't Rape! Delegate

Command, Controller, EventSubscriber, Listener - they all should be only delegating code to a model layer service. If you need to mutually call or inherit one in another, you're creating a code smell. That's a sign that you should **decouple common logic to a service** and pass it via constructor to both.

So instead of giving people the option to use your code wrong way, give them a service, they can call in e.g. the EventSubscriber.

### 4. Let Interface take the Responsibility

EventSubscriber has own interface, that guides you:

```php
<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MyCustomEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
    }
}
```

There is still a bit of magic... what should be in `getSubscribedEvents()` method? Honestly, I have no idea. I don't want to [remember what's written in code](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/). So I'll use PHPStorm:

<img src="/assets/images/posts/2019/sub/event_names.gif" class="img-thumbnail">

## What's Better with Event Subscriber?

- No config coding and all the related possible bugs ✅
- Adding new subscribers mean 0-work in config
- Adding new subscribed event mean 0-work in config
- No option to miss-use delegators ✅
- Easy to statically analyse ✅
- Easy to instantly upgrade ✅
- Any Symfony BC break will be easy to discover due to unused constant in exact line of code ✅

The trade-off worth the change

## Final Unrelated Tip: Constants over Strings

If you use `KernelEvents::VIEW` constants within PHP code, you make the code also easier to debug.

Where is `KernelEvents::VIEW` event actually dispatched? Just search `KernelEvents::VIEW` (or better `dispatch(KernelEvents::VIEW)`) in `/vendor` and PHPStorm will show you the exact line. If you'd look for a string, it will lead to a false source of `KernelEvents` (just a reference list of all Kernel events).

Also, when the event name is changed in a constant to `view_event`, you don't mind. If you have `view` in the config, good luck!

This makes using constants so fun. My rule of thumb is:

<blockquote class="blockquote text-center mb-4 mt-4">
    When the same string is used at 2 different classes,
    <br>
    it's worth creating a constant to make it typo-proof.
</blockquote>

Imagine you have some code like:

```php
# in class A
$configuration->setOption('resource');

# in class B
$input->getOption('resource');
```

Now you need to get this resource somewhere else. Was it "source", "sources", "resource" or "directory"? You don't care, constant autocomplete in PHPStorm tells you:

<img src="/assets/images/posts/2019/sub/constant.gif" class="img-thumbnail">

[Don't remember what you don't need to](/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/).

<br>

Happy coding!
