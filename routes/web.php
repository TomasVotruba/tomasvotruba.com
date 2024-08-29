<?php

declare(strict_types=1);

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class);

// blog
Route::get('/blog/{slug}', PostController::class)
    // include dots and slashes as well
    ->where('slug', '.*');


Route::redirect('/rss.xml', '/rss');
Route::get('/rss', RssController::class);

Route::get('/thumbnail/{title}.png', ThumbnailController::class);

Route::get('/bank',  function () {
    return view('bank', [
        'title' => 'Bank Contact',
    ]);
});

// redirects
Route::redirect('/contact', '/');
Route::redirect('/about', '/');
Route::redirect('/blog', '/#posts');


Route::redirect('/book', 'https://leanpub.com/rector-the-power-of-automated-refactoring');
Route::redirect('/book/the-power-of-automated-refactoring', 'https://leanpub.com/rector-the-power-of-automated-refactoring');
Route::redirect('/book-detail/rector-the-power-of-automated-refactoring', 'https://leanpub.com/rector-the-power-of-automated-refactoring');

