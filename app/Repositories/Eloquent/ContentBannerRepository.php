<?php

namespace App\Repositories\Eloquent;

use App\Models\ContentBanner;
use App\Repositories\Contracts\ContentBannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ContentBannerRepository implements ContentBannerRepositoryInterface
{
    public function forPage(string $page): Collection
    {
        return ContentBanner::query()->active()->forPage($page)->ordered()->get();
    }

    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return ContentBanner::query()->ordered()->paginate($perPage);
    }

    public function findById(int $id): ?ContentBanner
    {
        return ContentBanner::query()->find($id);
    }

    public function create(array $data): ContentBanner
    {
        return ContentBanner::query()->create($data);
    }

    public function update(ContentBanner $banner, array $data): ContentBanner
    {
        $banner->update($data);

        return $banner->fresh();
    }

    public function delete(ContentBanner $banner): void
    {
        $banner->delete();
    }
}
