<?php

namespace App\Repositories\Eloquent;

use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HeroSlideRepository implements HeroSlideRepositoryInterface
{
    public function allActive(): Collection
    {
        return HeroSlide::query()->active()->ordered()->get();
    }

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return HeroSlide::query()->ordered()->paginate($perPage);
    }

    public function findById(int $id): ?HeroSlide
    {
        return HeroSlide::query()->find($id);
    }

    public function create(array $data): HeroSlide
    {
        return HeroSlide::query()->create($data);
    }

    public function update(HeroSlide $heroSlide, array $data): HeroSlide
    {
        $heroSlide->update($data);

        return $heroSlide->fresh();
    }

    public function delete(HeroSlide $heroSlide): void
    {
        $heroSlide->delete();
    }
}
