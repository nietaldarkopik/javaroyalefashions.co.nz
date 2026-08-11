@extends('layouts.admin')

@section('title', 'Pages')
@section('page_title', 'Manage Pages')
@section('page_actions')
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Page</a>
@endsection

@section('main_content')
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr><th>Title</th><th>Slug</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><code>/page/{{ $page->slug }}</code></td>
                    <td>
                        @if ($page->is_published)
                        <span class="badge bg-success">Published</span>
                        @else
                        <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pages->links() }}</div>
@endsection
