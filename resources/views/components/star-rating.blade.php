@props(['rating' => 0])

{{-- Read-only star display. Supports half stars for averages (4.5 → ★★★★½). --}}
@php($value = round(((float) $rating) * 2) / 2)
<span {{ $attributes->merge(['class' => 'stars']) }} role="img" aria-label="{{ rtrim(rtrim(number_format((float) $rating, 1), '0'), '.') }} out of 5 stars">
  @for ($i = 1; $i <= 5; $i++)
    @if ($value >= $i)
    <i class="fa-solid fa-star" aria-hidden="true"></i>
    @elseif ($value >= $i - 0.5)
    <i class="fa-solid fa-star-half-stroke" aria-hidden="true"></i>
    @else
    <i class="fa-regular fa-star" aria-hidden="true"></i>
    @endif
  @endfor
</span>
