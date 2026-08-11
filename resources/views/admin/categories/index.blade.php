@extends('layouts.admin')

@section('title', 'Categories')
@section('page_title', 'Manage Categories')
@section('page_actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Category</a>
@endsection

@section('main_content')
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th></th><th>Name</th><th>Products</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td>
                        @if ($category->image_path)
                        <img src="{{ Storage::disk('public')->url($category->image_path) }}" style="width:40px;height:40px;object-fit:cover;" class="rounded">
                        @endif
                    </td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>
                        @if ($category->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category? Products stay, but become uncategorized.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
