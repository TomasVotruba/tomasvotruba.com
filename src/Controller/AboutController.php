<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;

final class AboutController extends Controller
{
    public function __invoke(): \Illuminate\Contracts\View\View
    {
        return \view('about', [
            'title' => "Hi, I'm Tomas, and I love legacy",
        ]);
    }
}
