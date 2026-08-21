@php $heroSlide = $heroSlide ?? null; @endphp

<div class="mb-3">
    <label class="form-label">Eyebrow</label>
    <input type="text" name="eyebrow" class="form-control" value="{{ old('eyebrow', $heroSlide?->eyebrow) }}" placeholder="Short label shown above the heading">
</div>

<div class="mb-3">
    <label class="form-label">Heading</label>
    <input type="text" name="heading" class="form-control" value="{{ old('heading', $heroSlide?->heading) }}">
</div>

<div class="mb-3">
    <label class="form-label">Subheading</label>
    <textarea name="subheading" rows="3" class="form-control">{{ old('subheading', $heroSlide?->subheading) }}</textarea>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Button Text</label>
        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $heroSlide?->button_text) }}" placeholder="e.g. Shop All Products">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Button URL</label>
        <input type="text" name="button_url" class="form-control" value="{{ old('button_url', $heroSlide?->button_url) }}" placeholder="e.g. /products">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Image</label>
    @if ($heroSlide?->image_path)
    <div class="mb-2"><img src="{{ Storage::disk('public')->url($heroSlide->image_path) }}" style="width:200px;height:112px;object-fit:cover;" class="rounded border"></div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
    <small class="form-text text-muted">Recommended 1600×900. Leave empty to keep the current image.</small>
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $heroSlide?->sort_order ?? 0) }}" style="max-width:120px;">
    <small class="form-text text-muted">Lower numbers show first.</small>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
        {{ old('is_active', $heroSlide?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
</div>

<button type="submit" class="btn btn-primary">Save Slide</button>
