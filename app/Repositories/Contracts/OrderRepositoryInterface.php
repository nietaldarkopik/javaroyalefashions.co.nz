<?php

namespace App\Repositories\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    /**
     * @param  array{status?: string, search?: string}  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function countByStatus(OrderStatus $status): int;

    public function recent(int $limit = 10): Collection;

    /**
     * Sum of grand_total for orders considered "sold" (paid/processing/completed).
     */
    public function totalSales(): float;
}
