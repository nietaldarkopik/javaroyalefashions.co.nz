<section class="section product-reviews" id="reviews">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">Customer Reviews</span>
        <h2>What Customers Say</h2>
      </div>
    </div>

    @if ($reviewSummary['count'] === 0)
    <div class="reviews-empty">
      <x-star-rating :rating="0" />
      <p>Be the first to review this product.</p>
      <span>Reviews come from verified customers after their order has been paid.</span>
    </div>
    @else
    <div class="reviews-layout">
      <aside class="reviews-summary">
        <div class="reviews-average">{{ number_format($reviewSummary['average'], 1) }}</div>
        <x-star-rating :rating="$reviewSummary['average']" />
        <p>Based on {{ $reviewSummary['count'] }} {{ Str::plural('review', $reviewSummary['count']) }}</p>

        <ul class="reviews-bars">
          @foreach ($reviewSummary['distribution'] as $star => $total)
          @php($percent = $reviewSummary['count'] > 0 ? round($total / $reviewSummary['count'] * 100) : 0)
          <li>
            <span class="reviews-bars-label">{{ $star }} <i class="fa-solid fa-star" aria-hidden="true"></i></span>
            <span class="reviews-bars-track"><span style="width: {{ $percent }}%"></span></span>
            <span class="reviews-bars-count">{{ $total }}</span>
          </li>
          @endforeach
        </ul>
      </aside>

      <div class="reviews-list">
        @foreach ($reviews as $review)
        <article class="review-card">
          <header>
            <x-star-rating :rating="$review->rating" />
            <time datetime="{{ $review->submitted_at->toDateString() }}">{{ $review->submitted_at->format('j M Y') }}</time>
          </header>
          @if ($review->title)
          <h4>{{ $review->title }}</h4>
          @endif
          <p>{!! nl2br(e($review->comment)) !!}</p>
          <footer>
            <span class="review-card-name">{{ $review->customer_name }}</span>
            <span class="review-card-verified"><i class="fa-solid fa-circle-check"></i> Verified buyer</span>
          </footer>
        </article>
        @endforeach

        @if ($reviews->hasPages())
        <div class="reviews-pagination">
          {{ $reviews->links('components.pagination') }}
        </div>
        @endif
      </div>
    </div>
    @endif
  </div>
</section>
