<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Customer::query()->withOrderStats()->latest();

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Customer
    {
        return Customer::query()->withOrderStats()
            ->with(['orders' => fn ($q) => $q->latest()])
            ->find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::query()->where('email', $email)->first();
    }

    public function firstOrCreateByEmail(array $data): Customer
    {
        /** @var Customer $customer */
        $customer = Customer::query()->firstOrCreate(
            ['email' => $data['email']],
            $data
        );

        // Keep contact details current for repeat customers who checked
        // out again under the same email with a new name/phone.
        $customer->fill([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ])->save();

        return $customer;
    }

    public function count(): int
    {
        return Customer::query()->count();
    }
}
