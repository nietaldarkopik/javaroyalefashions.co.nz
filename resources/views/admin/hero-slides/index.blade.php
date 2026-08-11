@extends('layouts.admin')

@section('title', 'Hero Slides')
@section('page_title', 'Manage Hero Slides')
@section('page_actions')
    <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Slide</a>
@endsection

@section('main_content')
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr><th></th><th>Heading</th><th>Order</th><th>Status</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($heroSlides as $heroSlide)
                <tr>
                    <td>
                        @if ($heroSlide->image_path)
                        <img src="{{ Storage::disk('public')->url($heroSlide->image_path) }}" style="width:60px;height:40px;object-fit:cover;" class="rounded">
                        @endif
                    </td>
                    <td>{{ $heroSlide->heading }}</td>
                    <td>{{ $heroSlide->sort_order }}</td>
                    <td>
                        @if ($heroSlide->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.hero-slides.edit', $heroSlide) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.hero-slides.destroy', $heroSlide) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this hero slide?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No hero slides yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $heroSlides->links() }}</div>
@endsection
