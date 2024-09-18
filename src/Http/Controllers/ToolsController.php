<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\ToolRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class ToolsController extends Controller
{
    public function __construct(
        private readonly ToolRepository $toolRepository
    ) {

    }

    public function __invoke(): View
    {
        return \view('tools', [
            'title' => 'Tools - What and When to use',
            'tools' => $this->toolRepository->fetchAll(),
        ]);
    }
}
