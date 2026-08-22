@extends('layouts.admin')

@section('title', 'Content Banners')
@section('page_title', 'Manage Content Banners')
@section('page_actions')
    <a href="{{ route('admin.content-banners.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Banner</a>
@endsection

@section('main_content')
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th></th><th>Heading</th><th>Shown On</th><th>Order</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($banners as $banner)
                <tr>
                    <td>
                        @if ($banner->image_path)
                        <img src="{{ Storage::disk('public')->url($banner->image_path) }}" style="width:60px;height:40px;object-fit:cover;" class="rounded">
                        @endif
                    </td>
                    <td>{{ $banner->heading ?: '—' }}</td>
                    <td>
                        @forelse (($banner->pages ?? []) as $page)
                        <span class="badge bg-light text-dark border">{{ \App\Models\ContentBanner::PAGES[$page] ?? $page }}</span>
                        @empty
                        <span class="text-muted">Nowhere yet</span>
                        @endforelse
                    </td>
                    <td>{{ $banner->sort_order }}</td>
                    <td>
                        @if ($banner->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.content-banners.edit', $banner) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.content-banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this banner?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No content banners yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $banners->links() }}</div>
@endsection
