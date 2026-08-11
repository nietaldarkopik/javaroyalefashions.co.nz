<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function listForFront(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->products->paginateForFront($filters, $perPage);
    }

    public function listForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->products->paginateForAdmin($filters, $perPage);
    }

    public function findBySlugOrFail(string $slug): Product
    {
        return $this->products->findBySlug($slug) ?? abort(404);
    }

    public function findByIdOrFail(int $id): Product
    {
        return $this->products->findById($id) ?? abort(404);
    }

    public function featured(int $limit = 8): Collection
    {
        return $this->products->featured($limit);
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return $this->products->related($product, $limit);
    }

    /**
     * @param  UploadedFile[]  $galleryImages
     */
    public function create(array $data, ?UploadedFile $image, array $galleryImages = []): Product
    {
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($image) {
            $data['image_path'] = $image->store('products', 'public');
        }

        $product = $this->products->create($data);

        $this->attachGalleryImages($product, $galleryImages);

        return $product;
    }

    /**
     * @param  UploadedFile[]  $galleryImages
     */
    public function update(Product $product, array $data, ?UploadedFile $image, array $galleryImages = []): Product
    {
        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        if ($image) {
            $this->deleteFile($product->image_path);
            $data['image_path'] = $image->store('products', 'public');
        }

        $product = $this->products->update($product, $data);

        $this->attachGalleryImages($product, $galleryImages);

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->deleteFile($product->image_path);

        foreach ($product->images as $image) {
            $this->deleteFile($image->image_path);
        }

        foreach ($product->variants as $variant) {
            $this->deleteFile($variant->image_path);
        }

        $this->products->delete($product);
    }

    public function deleteGalleryImage(Product $product, int $imageId): void
    {
        $image = $product->images()->findOrFail($imageId);
        $this->deleteFile($image->image_path);
        $image->delete();
    }

    public function createVariant(Product $product, array $data, ?UploadedFile $image): ProductVariant
    {
        if ($image) {
            $data['image_path'] = $image->store('products/variants', 'public');
        }

        return $product->variants()->create($data);
    }

    public function updateVariant(ProductVariant $variant, array $data, ?UploadedFile $image): ProductVariant
    {
        if ($image) {
            $this->deleteFile($variant->image_path);
            $data['image_path'] = $image->store('products/variants', 'public');
        }

        $variant->update($data);

        return $variant->fresh();
    }

    public function deleteVariant(ProductVariant $variant): void
    {
        $this->deleteFile($variant->image_path);
        $variant->delete();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * @param  UploadedFile[]  $galleryImages
     */
    private function attachGalleryImages(Product $product, array $galleryImages): void
    {
        $nextOrder = $product->images()->max('sort_order') + 1;

        foreach ($galleryImages as $index => $file) {
            $product->images()->create([
                'image_path' => $file->store('products', 'public'),
                'sort_order' => $nextOrder + $index,
            ]);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
