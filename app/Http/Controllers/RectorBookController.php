<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class RectorBookController extends Controller
{
    public function __invoke(): View
    {
        $rectorBook = new Book(
            'Rector - The Power of Automated Refactoring',
            <<<'CODE_SAMPLE'
In 2021 I wrote my first book about <a href="https://github.com/rectorphp/rector">Rector</a> with my favorite PHP hero and renowned writer &ndash; <a href="https://matthiasnoback.nl/">Matthias Noback</a>. It's a practical book with step-by-step examples.

        <h2>Dialog of Founder and First-Time User</h2>

        <p>
            The book was written by 2 people with different start position. By founder, who tells you how to get the best of tool, and by first time user, who ask for the basic yet important question to get full deep understanding.
        </p>

        <h2>Power Tool</h2>

        <p>
            This book will change the way you look at legacy code problems in your project. Do you need to change lot of files, get rid of patterns or unite framework usages from 3 to one? This book will teach you how to do it.
        </p>

        <h2>For Everyone</h2>

        <p>
            Do you think you need some special university education? No. Rector is designed to be super easy to use. You don't need deep knowledge of AST. The book will take you step by step from basics, through nodes and writing your first own rule.
        </p>


        <h2>Living Book</h2>

        <p>
            This book is something different then you're used to. As PHP and Rector will evolve, the book will get new releases too. Another part is how well is content explained. Have you read a book and found a place to improve?
            Let us know in <a href="https://github.com/rectorphp/the-power-of-automated-refactoring-feedback">special GitHub repository we created for your feedback</a>. Your contributions will be included in next book release.
        </p>
CODE_SAMPLE
            ,
            'https://d2sofvawe08yqg.cloudfront.net/rector-the-power-of-automated-refactoring/s_hero2x?1651754329',
            'https://leanpub.com/rector-the-power-of-automated-refactoring',
        );

        return \view('book/book_detail', [
            'title' => $rectorBook->getTitle(),
            'book' => $rectorBook,
        ]);
    }
}
