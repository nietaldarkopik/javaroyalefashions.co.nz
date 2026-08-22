<?php

namespace App\Repositories\Contracts;

use App\Models\ContentBanner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ContentBannerRepositoryInterface
{
    public function forPage(string $page): Collection;

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?ContentBanner;

    public function create(array $data): ContentBanner;

    public function update(ContentBanner $banner, array $data): ContentBanner;

    public function delete(ContentBanner $banner): void;
}
