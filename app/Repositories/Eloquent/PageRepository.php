<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PageRepository implements PageRepositoryInterface
{
    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return Page::query()->latest()->paginate($perPage);
    }

    public function allPublished(): Collection
    {
        return Page::query()->published()->orderBy('title')->get();
    }

    public function findBySlug(string $slug): ?Page
    {
        return Page::query()->published()->where('slug', $slug)->first();
    }

    public function findById(int $id): ?Page
    {
        return Page::query()->find($id);
    }

    public function create(array $data): Page
    {
        return Page::query()->create($data);
    }

    public function update(Page $page, array $data): Page
    {
        $page->update($data);

        return $page->fresh();
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }
}
