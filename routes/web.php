<?php

declare(strict_types=1);

use App\Enum\RouteName;
use App\Http\Controller\AboutController;
use App\Http\Controller\BlogController;
use App\Http\Controller\BookDetailController;
use App\Http\Controller\BooksController;
use App\Http\Controller\ContactController;
use App\Http\Controller\HomepageController;
use App\Http\Controller\PostController;
use App\Http\Controller\RssController;
use App\Http\Controller\ThumbnailController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

Route::get('/test/shiki', function () {
    $date = now()->format('Y-m-d H:i:s');

    $markdown = <<<EOT
# foo

$date

```php
<?php

Class Car {
    ///
}
```
EOT;

    $nodePath = app()->environment('production')
        ? '/root/.nvm/versions/node/v18.14.2/bin/node'
        : '/usr/local/bin/node';
    // which node

    $command = [
          0 => $nodePath,
          1 => "shiki.js",
          2 => '["<?php\n\nClass Bike {\n    \/\/\/\n}\n","php","github-dark",{"addLines":[],"deleteLines":[],"highlightLines":[],"focusLines":[]}]',
        ];

        $process = new Process(
            $command,
            base_path('/vendor/spatie/shiki-php/bin')
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();

    return $app
        ->highlightTheme('github-dark')
        ->toHtml($markdown);
});



Route::get('/', HomepageController::class)
    ->name(RouteName::HOMEPAGE);

Route::get('/about', AboutController::class)
    ->name(RouteName::ABOUT);

Route::get('/book-detail/{slug}', BookDetailController::class)
    ->name(RouteName::BOOK_DETAIL);

Route::get('/books', BooksController::class)
    ->name(RouteName::BOOKS);

Route::get('/blog/{slug}', PostController::class)
    ->name(RouteName::POST_DETAIL)
    // include dots and slahes as well
    ->where('slug', '.*');

Route::get('/blog', BlogController::class)
    ->name(RouteName::BLOG);

Route::get('/rss', RssController::class);
Route::get('/rss.xml', RssController::class)
    ->name(RouteName::RSS);

Route::get('/contact', ContactController::class)
    ->name(RouteName::CONTACT);

Route::get('/thumbnail/{title}.png', ThumbnailController::class)
    ->name(RouteName::POST_IMAGE);
