<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProductReviewStatusRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\ProductReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function __construct(
        private readonly ProductReviewService $reviews,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.reviews.index', [
            'reviews' => $this->reviews->paginateForAdmin($request->only(['status', 'product_id', 'order'])),
            'statuses' => ReviewStatus::options(),
            'products' => Product::query()->whereHas('reviews')->orderBy('name')->get(['id', 'name']),
            'pendingCount' => $this->reviews->countByStatus(ReviewStatus::Pending),
        ]);
    }

    public function updateStatus(UpdateProductReviewStatusRequest $request, ProductReview $review): RedirectResponse
    {
        $status = ReviewStatus::from($request->validated('status'));

        $this->reviews->moderate($review, $status, Auth::user());

        return back()->with('status', "Review marked as {$status->label()}.");
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $this->reviews->delete($review);

        return back()->with('status', 'Review deleted.');
    }
}
