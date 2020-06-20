---
id: 257
title: "Standalone&nbsp;Symfony Event&nbsp;Dispatcher from&nbsp;the&nbsp;Scratch"
perex: |
    Have you ever used Symfony Event Dispatcher? No?
    <br>
    <br>
    This post is an introduction to Event Dispatcher, how to use it, and in the end, you'll be able to cover 90 % use cases you'll ever need.

tweet: "New Post on #php 🐘 blog: #symfony Event Dispatcher from the Scratch"
tweet_image: "/assets/images/posts/2019/event_dispatcher_tweet.png"

updated_since: "June 2020"
updated_message: |
    Updated with Symfony [4.3 simple dispatching](https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching), PHP 7.4 syntax and [`::class`-based event names](/blog/2020/05/25/the-bulletproof-event-naming-for-symfony-event-dispatcher).
---

## What is the Main Purpose of Event Dispatcher?

- **Extend an application** in a certain place **without putting any code right there**.

<br>

This way, you can extend 3rd party packages without rewriting them. And also allow other users to reach your code without even touching it.

Not sure how that looks? You will - at the end of this article.


### Event Dispatcher

**This is the brain**. It stores all subscribers and calls events when you need to.


### Event

**This is the name of a place**. When something has happened in the application: *order is sent*, or *user is deleted*.


### Event Subscriber

This is **the action that happens when** we come to a specific event. When an order is sent (= Event), *send me a confirmation SMS* (= Event Subscriber). And *check that all the ordered products are on stock*.

**1 event can invoke MORE Event Subscribers**.


## Create First Subscriber in 3 Steps


### 1. Install via Composer

```bash
composer require symfony/event-dispatcher
```


### 2. Create Event Dispatcher

```php
// index.php
require_once __DIR__ . '/vendor/autoload.php';

// 1. create the Dispatcher
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher;

// 2. some event happend, we dispatch it
$eventDispatcher->dispatch('youtube.newVideoPublished'); // oh: event is just a string
```

Try it:

```bash
php index.php
```

Wow! Nothing happened...

That's ok because there is no Subscriber. So let's...


### 3. Create and Register Subscriber

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class NotifyMeOnVideoPublishedEventSubscriber implements EventSubscriberInterface
{
    public bool $isUserNotified = false;

    public static function getSubscribedEvents(): array
    {
        // in format ['event name' => 'public function name that will be called']
        return ['youtube.newVideoPublished' => 'notifyUserAboutVideo'];
    }

    public function notifyUserAboutVideo()
    {
        // some logic to send notification
        $this->isUserNotified = true;
    }
}
```

Let the Dispatcher know about the Subscriber.

```php
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher;

$eventSubscriber = new NotifyMeOnVideoPublishedEventSubscriber;
$eventDispatcher->addSubscriber($eventSubscriber);

// nothing happened, default value
var_dump($eventSubscriber->isUserNotified);
// false

// this calls our Subscriber
$eventDispatcher->dispatch('youtube.newVideoPublished');

// now it's changed
var_dump($eventSubscriber->isUserNotified);
// true
```

Run the code again from command line:

```bash
$ php index.php
int(0)
int(1)
```

And now you understand EventDispatcher. At least in 90 % use cases.

---

Still on? Let's get advanced.

What if we need to get the name of the Youtuber into the Subscriber?


## Event Objects to the Rescue!

The Event objects are basically [Value Objects](http://richardmiller.co.uk/2014/11/06/value-objects/). Pass a value in the constructor and get it with getter.


### 1. Create an Event Object

```php
use Symfony\Component\EventDispatcher\Event;

final class YoutuberNameEvent extends Event
{
    private string $youtuberName;

    public function __construct(string $youtuberName)
    {
        $this->youtuberName = $youtuberName;
    }

    public function getYoutuberName(): string
    {
        return $this->youtuberName;
    }
}
```


### 2. Use Event Object in Event Subscriber

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class NotifyMeOnVideoPublishedEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['youtube.newVideoPublished' => 'notifyUserAboutVideo'];
    }

    // Event Object is passed as method argument
    public function notifyUserAboutVideo(YoutuberNameEvent $youtuberNameEvent)
    {
        var_dump($youtuberNameEvent->getYoutuberName());
    }
}
```

### 3. Create an Object and Dispatch It

```php
$youtuberNameEvent = new YoutuberNameEvent('Jirka Král');

$eventDispatcher->dispatch($youtuberNameEvent);
```

And here is the result:

```bash
$ php index.php
string('Jirka Král')
```


## We Are 1 Step Further Now

You can now:

- understand basic Event workflow
- know what EventDispatcher and EventSubscriber are for
- and know-how to pass parameters via the Event object

### Where to go next?

Still hungry for knowledge? Check [Symfony documentation](http://symfony.com/doc/current/components/event_dispatcher.html) then.

But remember: ** practice is the best teacher**.

<br><br>

Happy coding!
