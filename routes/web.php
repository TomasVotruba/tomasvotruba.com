<?php

declare(strict_types=1);

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

Route::get('/', HomepageController::class);
Route::get('/about', AboutController::class);

Route::get('/book-detail/{slug}', BookDetailController::class);
Route::get('/books', BooksController::class);

Route::get('/blog/{slug}', PostController::class)
    // include dots and slahes as well
    ->where('slug', '.*');

Route::get('/blog', BlogController::class);
Route::get('/rss', RssController::class);
Route::get('/rss.xml', RssController::class);
Route::get('/contact', ContactController::class);

Route::get('/thumbnail/{title}.png', ThumbnailController::class);
