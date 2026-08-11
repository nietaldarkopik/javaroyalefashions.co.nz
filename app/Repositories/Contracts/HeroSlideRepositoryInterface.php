<?php

namespace App\Repositories\Contracts;

use App\Models\HeroSlide;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface HeroSlideRepositoryInterface
{
    public function allActive(): Collection;

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?HeroSlide;

    public function create(array $data): HeroSlide;

    public function update(HeroSlide $heroSlide, array $data): HeroSlide;

    public function delete(HeroSlide $heroSlide): void;
}
