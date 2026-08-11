@php $category = $category ?? null; @endphp

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $category?->name) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="3" class="form-control">{{ old('description', $category?->description) }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Image</label>
    @if ($category?->image_path)
    <div class="mb-2"><img src="{{ Storage::disk('public')->url($category->image_path) }}" style="width:80px;height:80px;object-fit:cover;" class="rounded border"></div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" style="max-width:120px;">
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
        {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
</div>

<button type="submit" class="btn btn-primary">Save Category</button>
