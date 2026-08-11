<?php

namespace App\Repositories\Eloquent;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::query()->with('customer')->latest();

        if (! empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Order
    {
        return Order::query()->with(['customer', 'items.product', 'paymentProofs'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::query()->with(['customer', 'items.product', 'paymentProofs'])
            ->where('order_number', $orderNumber)->first();
    }

    public function create(array $data): Order
    {
        return Order::query()->create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->fresh();
    }

    public function countByStatus(OrderStatus $status): int
    {
        return Order::query()->where('status', $status->value)->count();
    }

    public function recent(int $limit = 10): Collection
    {
        return Order::query()->with('customer')->latest()->limit($limit)->get();
    }

    public function totalSales(): float
    {
        return (float) Order::query()
            ->whereIn('status', [
                OrderStatus::Paid->value,
                OrderStatus::Processing->value,
                OrderStatus::Completed->value,
            ])
            ->sum('grand_total');
    }
}
