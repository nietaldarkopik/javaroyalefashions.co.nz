@php
    $banner = $banner ?? null;
    $selectedPages = old('pages', $banner?->pages ?? []);
@endphp

<div class="mb-3">
    <label class="form-label">Eyebrow</label>
    <input type="text" name="eyebrow" class="form-control" value="{{ old('eyebrow', $banner?->eyebrow) }}" placeholder="Short label shown above the heading">
</div>

<div class="mb-3">
    <label class="form-label">Heading</label>
    <input type="text" name="heading" class="form-control" value="{{ old('heading', $banner?->heading) }}">
</div>

<div class="mb-3">
    <label class="form-label">Text</label>
    <textarea name="body" rows="3" class="form-control">{{ old('body', $banner?->body) }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Button Text</label>
        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner?->button_text) }}" placeholder="e.g. Read Our Story">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Button URL</label>
        <input type="text" name="button_url" class="form-control" value="{{ old('button_url', $banner?->button_url) }}" placeholder="e.g. /page/about">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Background Image</label>
    @if ($banner?->image_path)
    <div class="mb-2"><img src="{{ Storage::disk('public')->url($banner->image_path) }}" style="width:200px;height:112px;object-fit:cover;" class="rounded border"></div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
    <small class="form-text text-muted">Recommended 1920×1080. Leave empty to keep the current image.</small>
</div>

<div class="mb-3">
    <label class="form-label">Shown On</label><br>
    @foreach (\App\Models\ContentBanner::PAGES as $key => $label)
    <div class="form-check form-check-inline">
        <input type="checkbox" name="pages[]" value="{{ $key }}" class="form-check-input" id="page-{{ $key }}"
            {{ in_array($key, $selectedPages ?? []) ? 'checked' : '' }}>
        <label class="form-check-label" for="page-{{ $key }}">{{ $label }}</label>
    </div>
    @endforeach
    <small class="form-text text-muted d-block">Pick every page this banner should appear on.</small>
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" style="max-width:120px;">
    <small class="form-text text-muted">Lower numbers show first when a page has more than one banner.</small>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
        {{ old('is_active', $banner?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
</div>

<button type="submit" class="btn btn-primary">Save Banner</button>
