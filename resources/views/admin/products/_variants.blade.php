<div class="card mt-4">
    <div class="card-header">Variants — Size, Color, Price</div>
    <div class="card-body">
        <p class="text-muted small">
            Add a variant for each size/color/etc. combination this product is sold in. Leave <strong>Price</strong>
            blank to use the base price above (${{ number_format($product->price, 2) }}). Customers must pick a
            variant before adding this product to their cart once at least one exists.
        </p>

        @forelse ($product->variants as $variant)
        <div class="variant-row">
            <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row align-items-end">
                    <div class="col-auto text-center">
                        @if ($variant->image_path)
                        <img src="{{ Storage::disk('public')->url($variant->image_path) }}" class="variant-thumb d-block mb-1">
                        @endif
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*" style="width:130px;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Size</label>
                        <input type="text" name="size" class="form-control form-control-sm" value="{{ $variant->size }}" placeholder="e.g. M">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Color</label>
                        <input type="text" name="color" class="form-control form-control-sm" value="{{ $variant->color }}" placeholder="e.g. Red">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Attribute Name</label>
                        <input type="text" name="attribute_name" class="form-control form-control-sm" value="{{ $variant->attribute_name }}" placeholder="e.g. Material">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Attribute Value</label>
                        <input type="text" name="attribute_value" class="form-control form-control-sm" value="{{ $variant->attribute_value }}" placeholder="e.g. Cotton">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-1">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control form-control-sm" value="{{ $variant->price }}" placeholder="base">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small mb-1">Stock</label>
                        <input type="number" min="0" name="stock_quantity" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}" required>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="variant-active-{{ $variant->id }}" {{ $variant->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small" for="variant-active-{{ $variant->id }}">Active</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                    </div>
                </div>
            </form>
            <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" class="text-right mt-1" onsubmit="return confirm('Delete this variant?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-danger p-0">Delete this variant</button>
            </form>
        </div>
        @empty
        <p class="text-muted small">No variants yet — this product is sold with just the base price/stock above.</p>
        @endforelse

        <hr>
        <h6 class="text-muted text-uppercase" style="font-size:12px; letter-spacing:0.04em;">Add New Variant</h6>
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-auto">
                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*" style="width:130px;">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Size</label>
                    <input type="text" name="size" class="form-control form-control-sm" placeholder="e.g. M">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Color</label>
                    <input type="text" name="color" class="form-control form-control-sm" placeholder="e.g. Red">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Attribute Name</label>
                    <input type="text" name="attribute_name" class="form-control form-control-sm" placeholder="e.g. Material">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Attribute Value</label>
                    <input type="text" name="attribute_value" class="form-control form-control-sm" placeholder="e.g. Cotton">
                </div>
                <div class="col-md-1">
                    <label class="form-label small mb-1">Price</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control form-control-sm" placeholder="base">
                </div>
                <div class="col-md-1">
                    <label class="form-label small mb-1">Stock</label>
                    <input type="number" min="0" name="stock_quantity" class="form-control form-control-sm" value="0" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Add Variant</button>
                </div>
            </div>
        </form>
    </div>
</div>
