@props(['setting'])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'logo']) }}>
    @if ($setting->logo_path)
    <img src="{{ Storage::disk('public')->url($setting->logo_path) }}" alt="{{ $setting->site_name }}">
    @if ($setting->show_site_name_with_logo)
    <span>{{ $setting->site_name }}</span>
    @endif
    @else
    {{ $setting->site_name }}
    @endif
</a>
