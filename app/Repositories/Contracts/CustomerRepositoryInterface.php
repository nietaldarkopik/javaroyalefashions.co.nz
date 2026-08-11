<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    /**
     * @param  array{search?: string}  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?Customer;

    public function findByEmail(string $email): ?Customer;

    public function firstOrCreateByEmail(array $data): Customer;

    public function count(): int;
}
