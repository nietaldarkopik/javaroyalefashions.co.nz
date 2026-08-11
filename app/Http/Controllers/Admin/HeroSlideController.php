<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHeroSlideRequest;
use App\Http\Requests\Admin\UpdateHeroSlideRequest;
use App\Models\HeroSlide;
use App\Services\HeroSlideService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function __construct(
        private readonly HeroSlideService $heroSlides,
    ) {}

    public function index(): View
    {
        return view('admin.hero-slides.index', [
            'heroSlides' => $this->heroSlides->listForAdmin(),
        ]);
    }

    public function create(): View
    {
        return view('admin.hero-slides.create');
    }

    public function store(StoreHeroSlideRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        $heroSlide = $this->heroSlides->create($data, $request->file('image'));

        return redirect()->route('admin.hero-slides.edit', $heroSlide)
            ->with('status', 'Hero slide created.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero-slides.edit', ['heroSlide' => $heroSlide]);
    }

    public function update(UpdateHeroSlideRequest $request, HeroSlide $heroSlide): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        $this->heroSlides->update($heroSlide, $data, $request->file('image'));

        return redirect()->route('admin.hero-slides.edit', $heroSlide)
            ->with('status', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $this->heroSlides->delete($heroSlide);

        return redirect()->route('admin.hero-slides.index')
            ->with('status', 'Hero slide deleted.');
    }
}
