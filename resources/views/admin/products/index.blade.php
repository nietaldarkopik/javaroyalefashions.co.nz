@extends('layouts.admin')

@section('title', 'Products')
@section('page_title', 'Manage Products')
@section('page_actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Product</a>
@endsection

@section('main_content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search name or SKU" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
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
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr>
                    <td>
                        @if ($product->image_path)
                        <img src="{{ Storage::disk('public')->url($product->image_path) }}" style="width:40px;height:40px;object-fit:cover;" class="rounded">
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>
                        @if ($product->variants_count > 0 && $product->price_range)
                        From ${{ number_format($product->price_range['min'], 2) }}
                        @elseif ($product->sale_price)
                        <span class="text-decoration-line-through text-muted small">${{ number_format($product->price, 2) }}</span>
                        ${{ number_format($product->sale_price, 2) }}
                        @else
                        ${{ number_format($product->price, 2) }}
                        @endif
                    </td>
                    <td>
                        @if ($product->variants_count > 0)
                        {{ $product->variants->sum('stock_quantity') }} total
                        @elseif ($product->stock_quantity < 1)
                        <span class="badge bg-danger">Out of stock</span>
                        @elseif ($product->stock_quantity < 5)
                        <span class="badge bg-warning">{{ $product->stock_quantity }} left</span>
                        @else
                        {{ $product->stock_quantity }}
                        @endif
                    </td>
                    <td>
                        @if ($product->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                        @if ($product->is_featured)
                        <span class="badge bg-info">Featured</span>
                        @endif
                        @if ($product->variants_count > 0)
                        <span class="badge bg-primary">{{ $product->variants_count }} variants</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
