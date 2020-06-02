---
id: 253
title: "The Bulletproof Event Naming For Symfony Event Dispatcher"
perex: |
    I wrote [intro to Symfony\EventDispatcher](https://pehapkari.cz/blog/2016/12/05/symfony-event-dispatcher/) and how to use it with simple event.
    <br><br>
    But when it comes to dispatching events, you can choose from 4 different ways. Which one to choose and why? Today I will show you pros and cons of them to make it easier for you.

tweet: "New Post on #php 🐘 blog: The Bulletproof Event Naming For #Symfony Event Dispatcher"

updated_since: "May 2020"
---

## 1. Start with *Stringly*

You can start with simple *string named event*:

```php
$postEvent = new PostEvent($post);
$this->eventDispatcher->dispatch('post_added', $postEvent);
```

Simple for start and easy to use for one place and one event.

One day I started to use in more places:

```php
$postEvent = new PostEvent($post);
$this->eventDispatcher->dispatch('post_add', $postEvent);
```

All looked good, but **the subscriber didn't work**. Fun time with event subscribers debugging was about to come.

Hour has passed. Event subscriber was registered as a service, tagged, collected by dispatcher... but I still couldn't find the issue. So I showed it to my colleague:

*Oh, you've got "post_add" there, but there should be "post_added".*

YAY! I copied the previous subscriber with "post_added" but **I made a typo** while dispatching event.

There must be a cure for this, I wished.

## 2. Group File with Events Names as Constants

Then I got inspired by Symfony [`ConsoleEvents` class](https://github.com/symfony/symfony/blob/d203ee33954f4e0c5b39cdc6224fe4fb96cac0c3/src/Symfony/Component/Console/ConsoleEvents.php) that collects all events from one domain in constants.

```php
final class PostEvents
{
    /**
     * This event is invoked when post is added.
     * It is called here @see \App\Post\PostService::add().
     * And @see \App\Events\PostAddedEvent class is passed.
     *
     * @var string
     */
    public const ON_POST_ADDED = 'post_added';

    /**
     * This event is invoked when post is published.
     * It is called here @see \App\Post\PostService::published().
     * And @see \App\Events\PostPublishedEvent class is passed.
     *
     * @var string
     */
    public const ON_POST_PUBLISHED = 'post_published';
}
```

Our first example will change from *stringly* to *strongly* typed:

```php
$postAddedEvent = new PostAddedEvent($post);
$this->eventDispatcher(PostEvents::ON_POST_ADDED, $postAddedEvent)
```

Also subscriber becomes typo-proof:

```php
final class TagPostSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [PostEvents::ON_POST_ADDED => 'tagPost'];
    }

    public function tagPost(PostAddedEvent $postAddedEvent): void
    {
        // ...
    }
}
```

### Pros

- All events are in one place.
- Easy to orientate for new programmer what events he or she can use.
- IDE helps you with constant autocompletion.

### Cons

- One class to store all events **breaks [open-closed principle](https://github.com/wataridori/solid-php-example/blob/master/2-open-closed-principle.php)**.
    - To add new event I have to put it here as well - human memory vulnerable.
- Your have to come up with **long annotation description above constant**:
    - where is used (one place or all),
    - link the event class with IDE-compatible notation, e.g. `EventClass` doesn't work in PHPStorm, but `@see EventClass` does

The more events you have the harder is this to maintain properly. With 5th event you might end up like this:

```php
final class PostEvents
{
    /**
     * This event is invoked when post is published.
     * It is called here @see \App\Post\PostService::published().
     * And @see \App\Events\PostPublishedEvent class is passed.
     *
     * @var string
     */
    public const ON_POST_PUBLISHED = 'post_published';

    // 3 more nicely annotated events...

    public const ON_POST_CHANGED = 'changed';
}
```

I wanted to respect open-closed principle, so global class was a no-go.

Maybe, I could put those...

## 3. Constant Names in Particular Event Classes

Like this:

```php
final class PostAddedEvent
{
    /**
     * @var string
     */
    public const NAME = 'post_added';

    /**
     * @var Post
     */
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
```

Our example is now *strongly* typed and **respects open-closed principle**:

```php
$postAddedEvent = new PostAddedEvent($post);
$this->eventDispatcher(PostAddedEvent::NAME, $postAddedEvent)
```

Like this!

### Pros

**All the above +**

- Easy to refactor event name.
- No more human error in event name typos.

### Cons

- You still need a human brain computation to keep `constant NAME = '...'` unique per-class.
- Beautiful place for error and long nights of debugging.

**Take a step back**: what is my goal?

I look for an identifier that is:

- **unique per class**
- **constant** (in both meanings if possible)
- **IDE friendly**
- **coupled to Event class** in any way
- doesn't allow me to make naming errors and typos

Can you see it? I think you do :)

## 4. Class-based Event Naming

```php
$postAddedEvent = new PostAddedEvent($post);
$this->eventDispatcher(PostAddedEvent::class, $postAddedEvent)
```

It could not be simpler and meets all the conditions!

### Pros

All 4 reasons above +

- **It's typo-proof**
- It uses PHP **native `::class` support**.
- It's addictively easy.

## Which Type Do You Like?

This is my story for event naming evolution. But what is yours - **which event naming system do you use**? I'm curious and ready to be wrong, so please let me know in the comments if you like it or do it any different way.

### Taking it Step Further

[Enumag](http://enumag.cz/) suggested such different way by removing first argument:

```php
public function dispatch(Event $event): void
{
    $this->eventDispatcher->dispatch(get_class($event), $event);
}
```

**And exactly this is [possible since Symfony 4.3](https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching)** (2019):

```php
$postAddedEvent = new PostAddedEvent($post);
$this->eventDispatcher($postAddedEvent);

// or in case we don't need to get changed content from the event

$this->eventDispatcher->dispatch(new PostAddedEvent($post));
```

<br>

Happy coding!
