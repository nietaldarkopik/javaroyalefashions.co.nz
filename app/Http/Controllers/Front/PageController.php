<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
    ) {}

    public function show(string $slug): View
    {
        return view('front.pages.show', [
            'page' => $this->pages->findBySlugOrFail($slug),
        ]);
    }
}
