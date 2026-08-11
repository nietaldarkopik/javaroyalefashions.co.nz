@extends('layouts.admin')

@section('title', 'Customers')
@section('page_title', 'Customers')

@section('main_content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Name, email, or phone" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>${{ number_format($customer->orders_sum_grand_total ?? 0, 2) }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
