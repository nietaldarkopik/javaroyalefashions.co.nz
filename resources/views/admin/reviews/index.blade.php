@extends('layouts.admin')

@section('title', 'Product Reviews')
@section('page_title', 'Product Reviews')
@section('page_actions')
    @if ($pendingCount > 0)
    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">{{ $pendingCount }} awaiting moderation</a>
    @endif
@endsection

@section('main_content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-3 mb-2 mb-md-0">
                <input type="text" name="order" class="form-control" placeholder="Order #" value="{{ request('order') }}">
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <select name="product_id" class="form-control">
                    <option value="">All Products</option>
                    @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $option)
                    <option value="{{ $option['value'] }}" @selected(request('status') === $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0 table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th>Product</th><th>Review</th><th>Customer</th><th>Order</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                <tr>
                    <td style="min-width:160px;">
                        @if ($review->product)
                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank">{{ $review->product->name }}</a>
                        <br><a href="{{ route('admin.products.edit', $review->product) }}" class="small text-muted">Edit product</a>
                        @else
                        <span class="text-muted">Deleted product</span>
                        @endif
                    </td>
                    <td style="min-width:260px; max-width:420px;">
                        <span class="text-warning">{{ str_repeat('★', $review->rating) }}</span><span class="text-muted">{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        @if ($review->title)
                        <div class="font-weight-bold">{{ $review->title }}</div>
                        @endif
                        <div class="small" style="white-space:pre-line; overflow-wrap:anywhere;">{{ $review->comment }}</div>
                    </td>
                    <td>
                        {{ $review->customer_name }}
                        <br><span class="text-muted small">{{ $review->submitted_at->format('d M Y H:i') }}</span>
                    </td>
                    <td>
                        @if ($review->order)
                        <a href="{{ route('admin.orders.show', $review->order) }}">{{ $review->order->order_number }}</a>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $review->status->badgeColor() }}">{{ $review->status->label() }}</span></td>
                    <td class="text-right text-nowrap">
                        @if ($review->status !== \App\Enums\ReviewStatus::Approved)
                        <form action="{{ route('admin.reviews.status', $review) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        @endif
                        @if ($review->status !== \App\Enums\ReviewStatus::Rejected)
                        <form action="{{ route('admin.reviews.status', $review) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Reject"><i class="fas fa-ban"></i> Reject</button>
                        </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review permanently? The customer will be able to review this product again while their link is valid.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No reviews found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $reviews->links() }}</div>
@endsection
