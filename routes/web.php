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
