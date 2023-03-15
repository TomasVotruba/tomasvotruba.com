---
id: 381
title: "To Route or To Action? That's&nbsp;the&nbsp;Question"

perex: |
    This week I've been working a lot on GPT in [Testgenai.com](https://testgenai.com/). I learn more and more Laravel as a side effect.

    During browsing of open-source projects written in Laravel, I've noticed syntax I was dreaming of since I started to use invokable controllers. What was it? Can it help or make code more complex?
---

Following feature is not exclusive for Laravel. Symfony has them and Nette allows them too - **single action controllers**:

```php
final class ProcessPhpFormController extends Controller
{
    public function __invoke(Request $request)
    {

    }
}
```

These classes have... eh, exactly 1 action. Like console command has one purpose and like event subscribers has purpose. It makes controller much easier to maintain.

<br>

This architecture allow us to refer the controller route directly in `/routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class);
```

We can use IDE to get to the controller class instantly. This feature is available in most PHP frameworks.


## Do you remember named services?

Before we jump into routing, I want to give you a little context and set the boundaries. Many year ago we used to use get element by it's reference. Do you want to get `App\Security\SecurityGuard` service? Name it in the config and then call:

```php
// in Laravel
$securityGuard = app()->make('security_guard');

// or in Symfony
$securityGuard = $this->get('security_guard');
```

Then a tiny but powerful feature came in PHP 5.5 - reference to class by a `*::class` notation. It might have sparked movement from string references to `*::class` references - ergo direct fetch.

Also, if there is just one exact instance of `security_guard` and has exclusive class of `App\Security\SecurityGuard`, why not use it directly and explicitly? IDE, PHPStan and Rector will thank us:

```php
use App\Security\SecurityGuard;

// in Laravel
$securityGuard = app()->make(SecurityGuard::class);

// or in Symfony
$securityGuard = $this->get(SecurityGuard::class);
```

This is typical today and we don't think about it anymore - we always use `App\Security\SecurityGuard` as reference, in constructor or during injections.

Context is loaded, so no we get back to routing.

## 1. `to_route()`

The routing is an array of urls, that point to specific controllers. An url string, that references specific controller class.

At the top of this post, we defined a controller action, that handles submitted form via POST.

<br>

Typical usage is in `<form>` tag:

```html
<form action="{{ route('...') }}" method="post">
```

<br>

Oh, we forgot something. To actually use `route()`, we have to add a route name:

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class)
    ->name('process_form');
```

Then we use the string in our template:

```html
<form action="{{ route('process_form') }}" method="post">
```

After the form is processed, we can redirect it back to homepage with:

```php
final class ProcessPhpFormController extends Controller
{
    public function __invoke(Request $request)
    {
        // process form

        return to_route('homepage');
    }
}
```

No we have 2 strings that we made up "process_form" and "homepage". Beware, it's not "process-form" with a `-`, but with `_`.

This can lead to typos, but can avoid that [by using constants](/blog/2020/12/21/5-new-combos-opened-by-symfony-52-and-php-80/#2-route-names-can-be-constants):

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class)
    ->name(RouteName::PROCESS_FORM);

Route::post('/', HomepageController::class)
    ->name(RouteName::HOMEPAGE);
```

<br>

That's a lot of code for single redirect to single controller, right? Where are we now?

* we have typo-proof route name
* we maintain a `RouteName` references that refer strings
* we have to always make up a new, unique string to avoid conflicts

I've been in this situation for years, because of confirmation bias. I like what I used, because I use it for a long time and I like it.

**There is no comparison, no A-B testing, no rational thinking**. My brain is just lazy and comfortable and chooses know path over anything else.

<blockquote class="blockquote text-center mt-5 mb-5">
    When we face unknown new way and known comfortable suffering,
    <br>
    we often choose the suffering, because we are used to it.
</blockquote>

<br>

Just a friendly reminder - *service name* was once in the past a *reference to class*:

```diff
 // in Laravel
-$securityGuard = app()->make('security_guard');
+$securityGuard = app()->make(\App\Security\SecurityGuard::class);

 // or in Symfony
-$securityGuard = $this->get('security_guard');
+$securityGuard = $this->get(\App\Security\SecurityGuard::class);
```

## 2. `to_action()`

A week ago I've noticed a tiny function in one Laravel project - [an `action()` function](https://laravel.com/docs/10.x/helpers#method-action).

It allows to use the controller directly, instead of middleman string:

```diff
-<form action="{{ route('process_form') }}" method="post">
+<form action="{{ action(\App\Controllers\ProcessPhpFormController::class) }}" method="post">
```

<br>

This is **where Blade design becomes super useful** - it's a PHP code, so we can:

* use IDE to jump directly to the controller
* we do not depend on stringy values, that have to be maintained on multiple places
* we can use IDE to change class names and the values will be updated


How about the redirects in controller?

```php
final class ProcessPhpFormController extends Controller
{
    public function __invoke(Request $request)
    {
        // process form

        return redirect()->action(HomepageController::class);
    }
}
```

Easy! Last but not least, we can drop the whole `RouteName` and named routes mechanism:

```diff
 use Illuminate\Support\Facades\Route;

-Route::post('/process-php-form', ProcessPhpFormController::class)
+Route::post('/process-php-form', ProcessPhpFormController::class);
-    ->name(RouteName::PROCESS_FORM);

-Route::post('/', HomepageController::class)
+Route::post('/', HomepageController::class);
-    ->name(RouteName::HOMEPAGE);
```

@todo fix the diff here too :)



## What benefits we have now?

* no more strings to maintain or typo
* references are clear in both templates and in controller
* **we can easily read the code without** jumping to `routes.php` - super useful for codereviews or reading an open-source code


<br>

## Which method do you use and why?

Be sure to follow wide-range replies on Twitter:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Which redirect in <a href="https://twitter.com/hashtag/laravel?src=hash&amp;ref_src=twsrc%5Etfw">#laravel</a> controllers do you prefer and why? <a href="https://t.co/9n0kCdXLNs">pic.twitter.com/9n0kCdXLNs</a></p>&mdash; Tomas Votruba (@VotrubaT) <a href="https://twitter.com/VotrubaT/status/1635401027171287041?ref_src=twsrc%5Etfw">March 13, 2023</a></blockquote>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

<br>

You can use your own or [open-sourced `to_action()` function](https://github.com/TomasVotruba/lavarle/commit/4f5c52972deab718eaedebbb1d6c9f862abc0b46).

<br>

Happy coding!
