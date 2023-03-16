---
id: 381
title: "To Route or To Action? That's&nbsp;the&nbsp;Question"

perex: |
    This week I've been working a lot on GPT in [Testgenai.com](https://testgenai.com/). I am learning more and more about Laravel as a side effect.

    During browsing open-source projects written in Laravel, I've noticed the syntax I have been dreaming of since I started to use invokable controllers. What was it? Can it make code cleaner or more complex? [I asked on Twitter](https://twitter.com/VotrubaT/status/1635401027171287041) and got such a variety of replies, that it deserves a blog post.
---

The following architecture makes entry points much clearer - **single action controllers**:

```php
final class ProcessPhpFormController extends Controller
{
    public function __invoke(Request $request)
    {

    }
}
```

These classes have... eh, exactly 1 action. Like console command has one purpose, and like event subscribers has a purpose. It makes the controller much easier to maintain.

<br>

This architecture allows us to refer to the controller route directly in `/routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class);
```

We can use IDE to get to the controller class instantly. This feature is available in most PHP frameworks.


## Do you remember the named services?

Before we jump into routing, I want to give you some context and set the boundaries. Many years ago, we used to use get element by its reference. Do you want to get the `App\Security\SecurityGuard` service? Name it in the config and then call:

```php
// in Laravel
$securityGuard = app()->make('security_guard');

// or in Symfony
$securityGuard = $this->get('security_guard');
```

Then a tiny but powerful feature came in PHP 5.5 - a reference to class by a `*::class` notation. It might have sparked movement from string references to `*::class` references - ergo, direct fetch.

Also, if there is just one exact instance of `security_guard` with an exclusive class of `App\Security\SecurityGuard`, why not use it directly and explicitly? IDE, PHPStan, and Rector will thank us:

```php
use App\Security\SecurityGuard;

// in Laravel
$securityGuard = app()->make(SecurityGuard::class);

// or in Symfony
$securityGuard = $this->get(SecurityGuard::class);
```

This is typical today, and we don't think about it anymore - we always use `App\Security\SecurityGuard` as a reference, in a constructor or during injections.

Context is loaded, so now we get back to routing.

## 1. `to_route()`

The routing is an array of urls that point to specific controllers, and an URL is a string that references a specific controller class.

At the top of this post, we defined a controller action that handles submitted forms via POST.

<br>

Typical usage is in `<form>` tag:

```html
<form action="{{ route('...') }}" method="post">
```

<br>

Oh, we forgot something. To use `route()`, we have to add a route name:

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class)
    ->name('process_form');
```

Then we use the string in our template:

```html
<form action="{{ route('process_form') }}" method="post">
```

After the form is processed, we can redirect it back to the homepage with the following:

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

Now we have 2 made up strings: "homepage" and "process_form"... or was it "process-form"?

This approach can lead to typos, but you can avoid that [by using constants](/blog/2020/12/21/5-new-combos-opened-by-symfony-52-and-php-80/#2-route-names-can-be-constants):

```php
use Illuminate\Support\Facades\Route;

Route::post('/process-php-form', ProcessPhpFormController::class)
    ->name(RouteName::PROCESS_FORM);

Route::post('/', HomepageController::class)
    ->name(RouteName::HOMEPAGE);
```

<br>

That's a lot of code for a single redirect to a single controller. Where are we now?

* We have a typo-proof route name
* We maintain a `RouteName` references that refer to strings
* We have to constantly make up a new, unique string to avoid conflicts

I've been in this situation for years because of confirmation bias: "I like what I use because I have used it for a long time and I like it."

**There is no comparison, A-B testing, or rational thinking**. My brain is lazy and comfortable and chooses a known path over anything else.

<blockquote class="blockquote text-center mt-5 mb-5">
    When we face unknown new way and known comfortable suffering,
    <br>
    we often choose to suffer because we are used to it.
</blockquote>

<br>

Just a friendly reminder - *service name* was once in the past a *reference to class*:

```diff
-$securityGuard = app()->make('security_guard');
+$securityGuard = app()->make(\App\Security\SecurityGuard::class);
```

## 2. `to_action()`

A week ago, I noticed a tiny function in one Laravel project - [an `action()` function](https://laravel.com/docs/10.x/helpers#method-action).

It allows using the controller directly instead of a middleman string:

```diff
-<form action="{{ route('process_form') }}" method="post">
+<form action="{{ action(\App\Controllers\ProcessPhpFormController::class) }}" method="post">
```

## The Power of Blade

This is **where Blade design becomes super helpful** - it's a PHP code, so we can:

* use IDE to jump directly to the controller
* We do not depend on stringy values that have to be maintained in multiple places
* We can use IDE to change class names, and the values will be updated


How about the redirects in the controller?

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

That was easy!

<br>

Last but not least, we can drop the whole `RouteName` and named routes mechanism:

```diff
 use Illuminate\Support\Facades\Route;

+Route::post('/process-php-form', ProcessPhpFormController::class);
-Route::post('/process-php-form', ProcessPhpFormController::class)
-    ->name(RouteName::PROCESS_FORM);

+Route::get('/', HomepageController::class);
-Route::get('/', HomepageController::class)
-    ->name(RouteName::HOMEPAGE);
```

## What benefits do we have now?

By moving from reference to an exact value, we can drop a whole 1/3 of complexity and logic:

* no more strings to maintain or typos
* references are explicit in both templates and the controller
* **we can easily read the code without** jumping to `routes.php` - super useful for code reviews or reading an open-source code


<br>

Tip: you can create your own or [open-sourced `to_action()` function](https://github.com/TomasVotruba/lavarle/commit/4f5c52972deab718eaedebbb1d6c9f862abc0b46).

```php
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

/**
 * @param class-string<Controller> $action
 * @param array<string, mixed> $parameters
 */
function to_action(string $action, array $parameters = []): RedirectResponse
{
    /** @var Redirector $redirector */
    $redirector = redirect();

    return $redirector->action($action, $parameters);
}
```

<br>

Happy coding!
