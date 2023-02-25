---
id: 351
title: "5 Constant Lists That Give&nbsp;Context to&nbsp;your Integers&nbsp;and&nbsp;Strings"
perex: |
    [Native enums](https://php.watch/versions/8.1/enums) is a new PHP 8.1 feature. This feature brings more focus on *what* enums are *where* we can use them.

    **Not on PHP 8.1 yet?** Don't worry, before that, enums were emulated by public constants. Few constant lists are already part of your favorite framework, ORM, or utils package for years.


    Today we look at 5 constant lists that you can use today to replace that `int` or `string` and give it a context.

tweet_image: /assets/images/posts/2022/kernel_events_usage.png
---

## Why even Bother with Enums?

```php
if ($duration < 14400) {
   return 'today';
}

return 'later';
```

<br>

We might ask, why even bother using some constants? Everybody knows 14400 is the number of minutes in a day or is it seconds... or is it 1440?

```diff
-if ($duration < 14400) {
+if ($duration < self::DAY_IN_MINUTES) {
    return 'today';
 }

 return 'later;
```

We are using constant to remove these questions. Somebody already did the calculation before me and was much better at the result. We avoid the crash of [thinking fast and thinking slow](https://www.amazon.com/Thinking-Fast-Slow-Daniel-Kahneman/dp/0374533555) at the same time - great book about how we think about others peoples' thinking.

<br>

There is [more pratcical benefits in using constants over scalars](/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs#10-Constants-over-Strings):

* we **give the context** to any reader, who will come 5 years later
* we reduce maintenance of all usages **to single place**
* and we don't have to ever think about it anymore

<br>

---

I've shared one such constant on Twitter, and it got much more traction and responses than I expected:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Did you know <a href="https://twitter.com/symfony?ref_src=twsrc%5Etfw">@symfony</a> offers constant codes for HTTP?<br><br>Since PHP 8.1 even more useful as enum! <a href="https://t.co/n1jpBYiu8i">pic.twitter.com/n1jpBYiu8i</a></p>&mdash; Tomas Votruba 🇺🇦 (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1502360490328080384?ref_src=twsrc%5Etfw">March 11, 2022</a></blockquote>

I've picked **the best and least known constant lists** in this post.

<br>

Ready for some hot constant code? Let's go:

## 1. HTTP Response Codes

After calling outside API service, we have to verify the result or the reason why it failed. Codes like 404 and 200 are mainstream, but what about the less known ones?

Can you tell me in 2 seconds what is the condition checking here?

```php
$response = $this->callExternalApi(...);
if ($response->getCode() === 403) {
    // what happened?
}
```

We know something is wrong... maybe something was not found? Maybe the URL is outdated?

*Maybe* is not good enough, and maybe, we can simply read it:

```diff
+use Symfony\Component\HttpFoundation\Response;

 $response = $this->callExternalApi(...);
-if ($response->getCode() === 403) {
+if ($response->getCode() === Response::HTTP_FORBIDDEN) {
     // what happened?
 }
```

The constant also differentiates HTTP response value, [Calgary telephone code](https://en.wikipedia.org/wiki/Area_code_403#:~:text=Area%20code%20403%20is%20a,assigned%20by%20AT%26T%20in%201947) and an integer id in a test.


### Where is the List?

- [`Symfony\Component\HttpFoundation\Response`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/HttpFoundation/Response.php#L24-L86)
- [`Nette\Http\IResponse`](https://github.com/nette/http/blob/17314395a830257e5db7167d5cccd1e6d1183ac9/src/Http/IResponse.php#L18-L79)


## 2. Request Methods

Where is a response, there must be a request first:

```php
$response = $this->callExternalApi(..., 'get');
```

We've all debugged an external API. If we're lucky, it has documentation. **If we're not lucky, the documentation is outdated** and the endpoint silently fails (I look at you Twitter, Meetup.com...).

A few hours later, we try the constant out of despair:

```diff
+use Symfony\Component\HttpFoundation\Request;

-$response = $this->callExternalApi(..., 'get');
+$response = $this->callExternalApi(..., Request::METHOD_GET);
```

And it works!

<br>

Why? Some API packages can mitigate the lowercased `"get"` to correct `"GET"`, but some can not. **We can mitigate bugs like these** by using a standardized constant that uses the correct version.

<br>

### Where is the List?

* [`Symfony\Component\HttpFoundation\Request`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/HttpFoundation/Request.php#L54-L63)
* [`Nette\Http\IRequest`](https://github.com/nette/http/blob/17314395a830257e5db7167d5cccd1e6d1183ac9/src/Http/IRequest.php#L21-L28)

<br>

P.S. Be creative... where else do we use `"GET"` or `"POST"` methods?

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class PostController extends AbstractController
{
    #[Route(path: '/blog/{slug}', methods: [Request::METHOD_GET])]
    public function __invoke(string $slug): Response
    {
    }
}
```


## 3. An hour, a Month, or a Year?

During my 19-years PHP developer career, I've used the calculator countless times just to produce correct *time numbers* like ~~`8640`~~... ehm, `86400`.

<br>

The moment I saw the following constant list first time, it blew both my mind and calculator away:

```diff
+use Nette\Utils\DateTime;

-if ($durationInSeconds < 86400) {
+if ($durationInSeconds < DateTime::DAY) {
     return 'less than a day';
 }

 return 'keep waiting';
```

The `DAY_IN_SECONDS` name would be better, but at least the constant has a comment to clarify this.

<br>

These constants come very handily with header expiration or [*time to leave* (TTLs)](https://en.wikipedia.org/wiki/Time_to_live) in cache:

```php
use Nette\Utils\DateTime;

$httpResponse->setHeader('Access-Control-Max-Age', 12 * DateTime::HOUR);
```

### Where is the List?

* [`Nette\Utils\DateTime`](https://github.com/nette/utils/blob/4f7d3da873c4e3762cf7ebb9d3073efcd1bd56fc/src/Utils/DateTime.php#L22-L38)



## 4. Accurate Event Subscribers

There are probably dozens of events in the Symfony event system we can hook into. We already [use subscribers over listeners](/blog/2019/05/16/don-t-ever-use-listeners/) and the `getSubscribedEvents()` method:

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => ['onKernelController'],
        ];
    }
}
```

<br>

Instead of strings for event names, we can use Symfony constant list:

```diff
+use Symfony\Component\HttpKernel\KernelEvents;

 return [
-    'kernel.controller' => ['onKernelController'],
+    KernelEvents::CONTROLLER => ['onKernelController'],
 ];
```

<br>

Thanks to this constant, we **open previously hidden knowledge** = where is the event invoked and what subscribers use the event:

<img src="/assets/images/posts/2022/kernel_events_usage.png" class="img-thumbnail" style="max-width: 35em">

### Where are the Lists?

* [`Symfony\Component\HttpKernel\KernelEvents`](https://github.com/symfony/symfony/blob/6.1/src/Symfony/Component/HttpKernel/KernelEvents.php#L28-L111)
* [`Symfony\Component\Console\ConsoleEvents`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/Console/ConsoleEvents.php#L24-L59)
* [`Symfony\Component\Security\Http\SecurityEvents`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/Security/Http/SecurityEvents.php#L19-L36)


## 5. Doctrine Column Types

Back in the PHP 7.4 days, we used to write Doctrine annotations in a weird string-like format, called comments, right above the property:

```php
use Doctrine\ORM\Mapping as ORM;

class Post
{
    /**
     * @ORM\Column(type="int")
     */
    private $wordCount;
}
```

Based on the `type=` part, the Doctrine was able to with database column type. Now I'm not sure about my memory, was it "int" or "integer"? We use "int" everywhere, even in native PHP type declarations, so that should be correct, right?

<br>

Well, time flew by, and now we can use [PHP 8.0-native attributes](https://php.watch/versions/8.0/attributes):

```php
use Doctrine\ORM\Mapping as ORM;

class Post
{
    #[ORM\Column(type: "int")]
    private $wordCount;
}
```

<br>

The syntax changed, PHPStan and Rector can work with these better, and PhpStorm autocomplete more reliable. **But my memory issue remains** - was it "int" or "integer"? Is there some solution for my memory leak?

Fortunately, there is:

```diff
 use Doctrine\ORM\Mapping as ORM;
+use Doctrine\DBAL\Types\Types;

 class Post
 {
-     #[ORM\Column(type: "int")]
+     #[ORM\Column(type: Types::INTEGER)]
     private $wordCount;
 }
```

Now we can state the intention - define a column to work with integer numbers,
and the knowledge is safely delegated to the tool itself - a constant it uses.

### Where is the List?

* [`Doctrine\DBAL\Types\Types`](https://github.com/doctrine/dbal/blob/83f779beaea1893c0bece093ab2104c6d15a7f26/src/Types/Types.php#L12-L36)


Thanks to [Alexander Schranz](https://twitter.com/alex_s_/status/1504064803719024645) for mentioning.

---

## Who do we Write Code For?

Now we know *what* constant enum-like lists we can use, but I'd like to return to *why* we use them. We all know that `200` is success code, and `301` is a permanent redirect... but do we know how many bits are in 1024 bytes?

<blockquote class="blockquote text-center">
"We don't write the code for use at present.<br>
We write for future fellow developers we'll never meet."
</blockquote>

If we pay more attention to writing standardized code now, future developers will thank us for making their lives easier. Maybe we will also thanks ourselves ;).

## Honorable Mentions

* [`Symfony\Component\Console\Command\Command::SUCCESS|FAILURE`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/Console/Command/Command.php#L36-L39) for command line exit codes in `Command::execute()` and CLI apps
* [`Psr\Log\LogLevel::*`](https://github.com/php-fig/log/blob/fe5ea303b0887d5caefd3d431c3e61ad47037001/src/LogLevel.php#L10-L17) for logging - thanks to [Oliver Nybroe](https://twitter.com/OliverNybroe/status/1503308677306011650)
* [`Symfony\Component\Form\FormEvents`](https://github.com/symfony/symfony/blob/2f27b39add8c8cdf7c70f2acfe8c9905eb56dfcc/src/Symfony/Component/Form/FormEvents.php#L29-L95) - thanks to [Shyim](https://twitter.com/Shyim97/status/1503030223608107019)

Have I missed a constant list that you use daily and makes your life easier? Please share in the comments or on Twitter to put it here too.

<br>

Happy coding!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
