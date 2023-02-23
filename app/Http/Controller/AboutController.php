<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class AboutController extends Controller
{
    public function __invoke(): View
    {
        return \view('about', [
            'title' => "Hi, I'm Tomas, and I love legacy",
        ]);
    }
}
