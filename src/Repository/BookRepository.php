<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Repository;

use TomasVotruba\Website\Entity\Book;
use TomasVotruba\Website\Exception\BookException;

final class BookRepository
{
    /**
     * @var Book[]
     */
    private array $books = [];

    public function __construct()
    {
        $this->books = [
            new Book(
                'Upgrade Every Day',
                <<<'CODE_SAMPLE'
<blockquote class="blockquote mt-2 blockquote-book">
"Upgrading software is like basic hygiene.
<br><br>
Do it daily for 10 minutes, and you'll live a long and healthy life.
<br><br>
Skip it for a year or two, and you'll find yourself in a hospital."
</blockquote>

<p>
Yet, we see software upgrades as something to avoid and postpone as long as possible. We think it's a large operation, so we'll have to stop everything we do and deep dive into the risky process of upgrading that might take years to handle. Don't touch it if it works, right?
 </p>

<br>

<h2>What if we take the opposite approach?</h2>

<p>
    Instead of postponing the problem as an acceptable risk, whilte it piles up slowly to mission-impossible size, we face it right away! We tackle it the moment it is created - now.
</p>

<h2>We Upgrade Every Day</h2>

<br>

<p>
    I've used this approach for the last 8 years with hands-on upgrades and clients worldwide. This book distills the approaches that are time-tested and proven by practice.
</p>

<h2>What will You Learn?</h2>

<ul>
    <li class="mb-2">Why upgrading is easy, but refactoring not?</li>
    <li class="mb-2">How to approach a legacy project you've never seen before?</li>
    <li class="mb-2">How to prevent your current project from ever becoming a legacy code base and avoid issues piling up quietly but steadily?</li>
    <li class="mb-2">How to use Occam's razor to a problem of any size?</li>
    <li class="mb-2">What is the "touch the ceiling" technique, and how to use it in desperate situations where there is no possible way to continue the upgrade?</li>
    <li class="mb-2">Set of go-to solutions to the vast scale of problems - from a PHP library that has been removed from Packagist, a project that has no type declarations whatsoever, to major framework upgrades</li>
</ul>

<br>

<p>
Every upgrade has its low-hanging fruit. This book shows you how to find it, start slowly, and climb high with confidence. The next time you'll do the upgrade, it will be as easy as taking a shower.
</p>
CODE_SAMPLE
                ,
                'https://d2sofvawe08yqg.cloudfront.net/upgrade-every-day/s_hero2x?1660495665',
                'https://leanpub.com/upgrade-every-day',
                false
            ),

            new Book(
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
                true
            ),
        ];
    }

    /**
     * @return Book[]
     */
    public function fetchAll(): array
    {
        return $this->books;
    }

    public function getBySlug(string $slug): Book
    {
        foreach ($this->books as $book) {
            if ($book->getSlug() === $slug) {
                return $book;
            }
        }

        $errorMessage = sprintf('Book slug "%s" was not found', $slug);
        throw new BookException($errorMessage);
    }
}
