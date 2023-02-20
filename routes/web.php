<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use TomasVotruba\Website\Controller\AboutController;
use TomasVotruba\Website\Controller\BlogController;
use TomasVotruba\Website\Controller\BookDetailController;
use TomasVotruba\Website\Controller\BooksController;
use TomasVotruba\Website\Controller\ContactController;
use TomasVotruba\Website\Controller\HomepageController;
use TomasVotruba\Website\Controller\PostController;
use TomasVotruba\Website\Controller\PostImageController;
use TomasVotruba\Website\Controller\RssController;
use TomasVotruba\Website\ValueObject\RouteName;

Route::get('/', HomepageController::class)
    ->name(RouteName::HOMEPAGE);

Route::get('/about', AboutController::class)
    ->name(RouteName::ABOUT);

Route::get('/book-detail/{slug}', BookDetailController::class)
    ->name(RouteName::BOOK_DETAIL);

Route::get('/books', BooksController::class)
    ->name(RouteName::BOOKS);

Route::get('/post/{slug}', PostController::class)
    ->name(RouteName::POST_DETAIL)
    ->where('slug', '.*');

Route::get('/blog', BlogController::class)
    ->name(RouteName::BLOG);

Route::get('/rss', RssController::class)
    ->name(RouteName::RSS);

Route::get('/contact', ContactController::class)
    ->name(RouteName::CONTACT);

Route::get('/thumbnail/{title}.png', PostImageController::class)
    ->name(RouteName::POST_IMAGE);
