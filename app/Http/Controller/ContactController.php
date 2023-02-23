<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class ContactController extends Controller
{
    public function __invoke(): View
    {
        return \view('contact', [
            'title' => 'Get in Touch',
        ]);
    }
}
