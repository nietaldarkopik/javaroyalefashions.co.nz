<?php

namespace App\Repositories\Contracts;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PageRepositoryInterface
{
    public function paginateForAdmin(int $perPage = 20): LengthAwarePaginator;

    public function allPublished(): Collection;

    public function findBySlug(string $slug): ?Page;

    public function findById(int $id): ?Page;

    public function create(array $data): Page;

    public function update(Page $page, array $data): Page;

    public function delete(Page $page): void;
}
