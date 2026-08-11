<?php

namespace App\Services;

use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class HeroSlideService
{
    public function __construct(
        private readonly HeroSlideRepositoryInterface $heroSlides,
    ) {}

    public function allActive(): Collection
    {
        return $this->heroSlides->allActive();
    }

    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->heroSlides->paginateForAdmin($perPage);
    }

    public function findByIdOrFail(int $id): HeroSlide
    {
        return $this->heroSlides->findById($id) ?? abort(404);
    }

    public function create(array $data, ?UploadedFile $image): HeroSlide
    {
        if ($image) {
            $data['image_path'] = $image->store('hero-slides', 'public');
        }

        return $this->heroSlides->create($data);
    }

    public function update(HeroSlide $heroSlide, array $data, ?UploadedFile $image): HeroSlide
    {
        if ($image) {
            if ($heroSlide->image_path) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
            $data['image_path'] = $image->store('hero-slides', 'public');
        }

        return $this->heroSlides->update($heroSlide, $data);
    }

    public function delete(HeroSlide $heroSlide): void
    {
        if ($heroSlide->image_path) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }

        $this->heroSlides->delete($heroSlide);
    }
}
