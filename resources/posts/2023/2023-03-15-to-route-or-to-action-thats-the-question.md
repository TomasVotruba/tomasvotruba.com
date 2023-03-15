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

## `to_route()`

The routing is an array of urls, that point to specific controllers. An url string, that references specific controller class.

At the top of this post, we defined a controller action, that handles submitted form via POST.

Typical usage is in `<form>` tag:

```html
<form action="{{ route('...') }}" method="post">
```





