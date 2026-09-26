@extends('layouts.front')

@section('title', 'Review Your Purchase — '.$siteSetting->site_name)

@section('content')

<div class="wrap review-order">
  <div class="review-order-head">
    <span class="eyebrow">Order {{ $order->order_number }} &middot; {{ $order->created_at->format('j M Y') }}</span>
    <h1>Review Your Purchase</h1>
    <p>Tell us what you think of the products from your order. Review as many as you like &mdash; you can skip any you'd rather not rate.</p>
  </div>

  @if (session('review_status'))
  <div class="review-alert review-alert--success" role="status">
    <i class="fa-solid fa-circle-check"></i> {{ session('review_status') }}
  </div>
  @endif

  @if ($errors->any())
  <div class="review-alert review-alert--error" role="alert">
    <i class="fa-solid fa-circle-exclamation"></i>
    @if ($errors->has('reviews') || $errors->has('website'))
      {{ $errors->first('reviews') ?: $errors->first('website') }}
    @else
      Please check the highlighted fields below.
    @endif
  </div>
  @endif

  @php
    $pending = $lines->reject(fn ($line, $productId) => $existingReviews->has($productId));
  @endphp

  @if ($pending->isEmpty())
  <div class="review-alert review-alert--success">
    <i class="fa-solid fa-heart"></i> You've reviewed every product in this order. Thank you for your feedback!
  </div>
  @endif

  <form action="{{ route('reviews.order.store', $token) }}" method="POST" class="review-order-form" id="review-order-form" novalidate>
    @csrf
    {{-- Honeypot: invisible to people, tempting to bots. --}}
    <div class="review-hp" aria-hidden="true">
      <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    @foreach ($lines as $productId => $line)
      @php
        $item = $line['item'];
        $product = $item->product;
        $image = $item->variant?->image_path ?: $product?->image_path;
        $existing = $existingReviews->get($productId);
        $field = "reviews.{$productId}";
        $hasError = $errors->has("{$field}.*");
      @endphp

      <article @class(['review-item', 'review-item--done' => $existing, 'review-item--error' => $hasError]) id="review-product-{{ $productId }}">
        <div class="review-item-product">
          <div class="ph review-item-thumb">
            @if ($image)
            <img src="{{ Storage::disk('public')->url($image) }}" alt="{{ $item->product_name }}">
            @else
            <span>PHOTO</span>
            @endif
          </div>
          <div>
            <h3>{{ $product?->name ?? $item->product_name }}</h3>
            @if ($line['variant_labels']->isNotEmpty())
            <div class="review-item-variant">{{ $line['variant_labels']->implode(', ') }}</div>
            @endif
            @if ($existing)
            <span class="review-badge"><i class="fa-solid fa-check"></i> Reviewed</span>
            @endif
          </div>
        </div>

        @if ($existing)
        <div class="review-item-submitted">
          <x-star-rating :rating="$existing->rating" />
          @if ($existing->title)
          <strong>{{ $existing->title }}</strong>
          @endif
          <p>{!! nl2br(e($existing->comment)) !!}</p>
          <span class="review-item-meta">
            Submitted {{ $existing->submitted_at->format('j M Y') }} &middot;
            {{ $existing->status === \App\Enums\ReviewStatus::Approved ? 'Published' : 'Awaiting approval' }}
          </span>
        </div>
        @else
        <div class="review-item-fields">
          <fieldset class="form-row review-rating-row">
            <legend>Your rating</legend>
            <div class="star-input">
              @foreach ([5, 4, 3, 2, 1] as $star)
              <input type="radio" id="rating-{{ $productId }}-{{ $star }}" name="reviews[{{ $productId }}][rating]" value="{{ $star }}"
                     @checked((string) old("{$field}.rating") === (string) $star)>
              <label for="rating-{{ $productId }}-{{ $star }}" title="{{ $star }} star{{ $star > 1 ? 's' : '' }}">
                <i class="fa-solid fa-star" aria-hidden="true"></i>
                <span class="sr-only">{{ $star }} star{{ $star > 1 ? 's' : '' }}</span>
              </label>
              @endforeach
            </div>
            <button type="button" class="review-clear" data-review-clear="{{ $productId }}">Skip this product</button>
            @error("{$field}.rating")<span class="field-error">{{ $message }}</span>@enderror
          </fieldset>

          <div class="form-row">
            <label for="title-{{ $productId }}">Review title <span class="optional">(optional)</span></label>
            <input type="text" id="title-{{ $productId }}" name="reviews[{{ $productId }}][title]"
                   maxlength="{{ config('reviews.title_max') }}" value="{{ old("{$field}.title") }}" placeholder="Sum it up in a few words">
            @error("{$field}.title")<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-row">
            <label for="comment-{{ $productId }}">Your review</label>
            <textarea id="comment-{{ $productId }}" name="reviews[{{ $productId }}][comment]" rows="4"
                      maxlength="{{ config('reviews.comment_max') }}" placeholder="What did you like? How's the fit and quality?">{{ old("{$field}.comment") }}</textarea>
            @error("{$field}.comment")<span class="field-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-row">
            <label for="name-{{ $productId }}">Name shown with your review</label>
            <input type="text" id="name-{{ $productId }}" name="reviews[{{ $productId }}][customer_name]"
                   maxlength="{{ config('reviews.name_max') }}" value="{{ old("{$field}.customer_name", $defaultName) }}">
            @error("{$field}.customer_name")<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>
        @endif
      </article>
    @endforeach

    @if ($pending->isNotEmpty())
    <div class="review-submit-bar">
      <p>Reviews are checked by our team before they appear on the site.</p>
      <button type="submit" class="btn btn--rust" id="review-submit-btn">Submit All Reviews</button>
    </div>
    @else
    <div class="review-submit-bar">
      <a href="{{ route('products.index') }}" class="btn btn--outline">Continue Shopping</a>
    </div>
    @endif
  </form>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('review-order-form');
    if (!form) return;

    // "Skip this product": clear everything the customer typed for it, so
    // the blank entry is ignored on submit instead of failing validation.
    // The display name is left as-is since it's prefilled and doesn't
    // count towards a review on its own.
    form.querySelectorAll('[data-review-clear]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const item = document.getElementById('review-product-' + btn.dataset.reviewClear);
            item.querySelectorAll('input[type=radio]').forEach((r) => { r.checked = false; });
            item.querySelectorAll('input[name$="[title]"], textarea').forEach((f) => { f.value = ''; });
        });
    });

    // Block double submits; the server ignores duplicates anyway.
    form.addEventListener('submit', () => {
        const btn = document.getElementById('review-submit-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Submitting…';
        }
    });
})();
</script>
@endpush

@endsection
