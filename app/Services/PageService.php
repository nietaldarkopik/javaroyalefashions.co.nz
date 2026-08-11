<?php

namespace App\Services;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PageService
{
    public function __construct(
        private readonly PageRepositoryInterface $pages,
    ) {}

    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->pages->paginateForAdmin($perPage);
    }

    public function findBySlugOrFail(string $slug): Page
    {
        return $this->pages->findBySlug($slug) ?? abort(404);
    }

    public function findByIdOrFail(int $id): Page
    {
        return $this->pages->findById($id) ?? abort(404);
    }

    public function create(array $data): Page
    {
        return $this->pages->create($data);
    }

    public function update(Page $page, array $data): Page
    {
        return $this->pages->update($page, $data);
    }

    public function delete(Page $page): void
    {
        $this->pages->delete($page);
    }
}
