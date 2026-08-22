<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContentBannerRequest;
use App\Http\Requests\Admin\UpdateContentBannerRequest;
use App\Models\ContentBanner;
use App\Services\ContentBannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentBannerController extends Controller
{
    public function __construct(
        private readonly ContentBannerService $banners,
    ) {}

    public function index(): View
    {
        return view('admin.content-banners.index', [
            'banners' => $this->banners->listForAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('admin.content-banners.create');
    }

    public function store(StoreContentBannerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        $banner = $this->banners->create($data, $request->file('image'));

        return redirect()->route('admin.content-banners.edit', $banner)
            ->with('status', 'Banner created.');
    }

    public function edit(ContentBanner $contentBanner): View
    {
        return view('admin.content-banners.edit', ['banner' => $contentBanner]);
    }

    public function update(UpdateContentBannerRequest $request, ContentBanner $contentBanner): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        $this->banners->update($contentBanner, $data, $request->file('image'));

        return redirect()->route('admin.content-banners.edit', $contentBanner)
            ->with('status', 'Banner updated.');
    }

    public function destroy(ContentBanner $contentBanner): RedirectResponse
    {
        $this->banners->delete($contentBanner);

        return redirect()->route('admin.content-banners.index')
            ->with('status', 'Banner deleted.');
    }
}
