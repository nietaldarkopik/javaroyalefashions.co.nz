<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
    ) {}

    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => $this->pages->listForAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $page = $this->pages->create($request->validated());

        return redirect()->route('admin.pages.edit', $page)
            ->with('status', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->pages->update($page, $request->validated());

        return redirect()->route('admin.pages.edit', $page)
            ->with('status', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->pages->delete($page);

        return redirect()->route('admin.pages.index')
            ->with('status', 'Page deleted.');
    }
}
