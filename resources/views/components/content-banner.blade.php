@props(['banner'])

<section class="video-banner reveal">
  <div class="ph">
    @if ($banner->image_path)
    <img src="{{ Storage::disk('public')->url($banner->image_path) }}" alt="{{ $banner->heading }}">
    @else
    <span>BANNER PHOTO — 1920×1080</span>
    @endif
  </div>
  <div class="video-banner-content">
    @if ($banner->eyebrow)
    <span class="eyebrow">{{ $banner->eyebrow }}</span>
    @endif
    @if ($banner->heading)
    <h2>{{ $banner->heading }}</h2>
    @endif
    @if ($banner->body)
    <p>{{ $banner->body }}</p>
    @endif
    @if ($banner->button_text && $banner->button_url)
    <a href="{{ $banner->button_url }}" class="btn">{{ $banner->button_text }}</a>
    @endif
  </div>
</section>
