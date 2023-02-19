<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function __invoke(): View
    {
        return \view('contact', [
            'title' => 'Get in Touch',
        ]);
    }
}
