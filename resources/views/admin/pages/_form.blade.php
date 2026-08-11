@php $page = $page ?? null; @endphp

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $page?->title) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $page?->slug) }}" required>
    <div class="form-text">Used in the URL: /page/&lt;slug&gt;. Lowercase letters, numbers, dashes only.</div>
</div>

<div class="mb-3">
    <label class="form-label">Content</label>
    <textarea name="content" rows="10" class="form-control">{{ old('content', $page?->content) }}</textarea>
    <div class="form-text">HTML is allowed and rendered as-is on the page.</div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Meta Title</label>
        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page?->meta_title) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Meta Description</label>
        <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page?->meta_description) }}">
    </div>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published"
        {{ old('is_published', $page?->is_published ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_published">Published (visible on the storefront)</label>
</div>

<button type="submit" class="btn btn-primary">Save Page</button>
