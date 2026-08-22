<?php

namespace App\Services;

use App\Models\ContentBanner;
use App\Repositories\Contracts\ContentBannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ContentBannerService
{
    public function __construct(
        private readonly ContentBannerRepositoryInterface $banners,
    ) {}

    public function forPage(string $page): Collection
    {
        return $this->banners->forPage($page);
    }

    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->banners->paginateForAdmin($perPage);
    }

    public function findByIdOrFail(int $id): ContentBanner
    {
        return $this->banners->findById($id) ?? abort(404);
    }

    public function create(array $data, ?UploadedFile $image): ContentBanner
    {
        if ($image) {
            $data['image_path'] = $image->store('content-banners', 'public');
        }

        return $this->banners->create($data);
    }

    public function update(ContentBanner $banner, array $data, ?UploadedFile $image): ContentBanner
    {
        if ($image) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $image->store('content-banners', 'public');
        }

        return $this->banners->update($banner, $data);
    }

    public function delete(ContentBanner $banner): void
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $this->banners->delete($banner);
    }
}
