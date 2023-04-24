<?php

declare(strict_types=1);

namespace App\Http\Controllers\Helinvoice;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class InvoiceController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('helinvoice/invoice', [
            'title' => 'Invoice Converter',
        ]);
    }
}
