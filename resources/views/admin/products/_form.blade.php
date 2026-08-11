@php $product = $product ?? null; @endphp

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product?->name) }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">— None —</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product?->sku) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Short Description</label>
            <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $product?->short_description) }}" maxlength="255">
        </div>

        <div class="mb-3">
            <label class="form-label">Full Description</label>
            <textarea name="description" rows="6" class="form-control">{{ old('description', $product?->description) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Price (NZD)</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product?->price) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Sale Price (optional)</label>
                <input type="number" step="0.01" min="0" name="sale_price" class="form-control" value="{{ old('sale_price', $product?->sale_price) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Stock Quantity</label>
                <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Weight (kg, optional — for future shipping-rate integration)</label>
            <input type="number" step="0.01" min="0" name="weight_kg" class="form-control" value="{{ old('weight_kg', $product?->weight_kg) }}" style="max-width:160px;">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product?->meta_title) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Meta Description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $product?->meta_description) }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Primary Image</label>
            <div class="upload-dropzone" id="primary-dropzone">
                <div class="upload-preview" id="primary-preview">
                    @if ($product?->image_path)
                    <img src="{{ Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}">
                    @else
                    <div class="upload-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Click or drag an image here</span>
                    </div>
                    @endif
                </div>
                <input type="file" name="image" id="primary-image-input" accept="image/*" hidden>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Gallery Images (optional, multiple)</label>
            <div class="upload-dropzone upload-dropzone--gallery" id="gallery-dropzone">
                <div class="upload-placeholder">
                    <i class="fas fa-images"></i>
                    <span>Click or drag images here — multiple allowed</span>
                </div>
                <input type="file" name="gallery_images[]" id="gallery-images-input" accept="image/*" multiple hidden>
            </div>

            @if ($product?->images->isNotEmpty())
            <div class="gallery-grid mt-3">
                @foreach ($product->images as $image)
                <div class="gallery-thumb">
                    <img src="{{ Storage::disk('public')->url($image->image_path) }}" alt="">
                    <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" onsubmit="return confirm('Remove this image?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="gallery-thumb-remove"><i class="fas fa-trash"></i> Remove</button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            <div class="gallery-grid mt-2" id="gallery-pending-preview"></div>
        </div>

        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured"
                {{ old('is_featured', $product?->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured on homepage</label>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary">Save Product</button>

<script>
(function () {
    function wireDropzone(zoneId, inputId, onFiles) {
        const zone = document.getElementById(zoneId);
        const input = document.getElementById(inputId);
        if (!zone || !input) return;

        zone.addEventListener('click', () => input.click());
        zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('is-dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('is-dragover'));
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('is-dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
        input.addEventListener('change', () => onFiles(input.files));
    }

    wireDropzone('primary-dropzone', 'primary-image-input', (files) => {
        if (!files.length) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('primary-preview').innerHTML = '<img src="' + e.target.result + '" alt="">';
        };
        reader.readAsDataURL(files[0]);
    });

    wireDropzone('gallery-dropzone', 'gallery-images-input', (files) => {
        const preview = document.getElementById('gallery-pending-preview');
        preview.innerHTML = '';
        Array.from(files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'gallery-thumb gallery-thumb--pending';
                div.innerHTML = '<img src="' + e.target.result + '" alt=""><span class="gallery-thumb-badge">New</span>';
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
})();
</script>
