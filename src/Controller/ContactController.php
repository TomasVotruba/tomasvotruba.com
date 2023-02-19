<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;

final class ContactController extends Controller
{
    public function __invoke(): \Illuminate\Contracts\View\View
    {
        return \view('contact', [
            'title' => 'Get in Touch',
        ]);
    }
}
