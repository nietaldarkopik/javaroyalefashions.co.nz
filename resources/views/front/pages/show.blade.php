@extends('layouts.front')

@section('title', ($page->meta_title ?: $page->title).' — '.$siteSetting->site_name)
@section('meta_description', $page->meta_description ?: '')

@section('content')

<div class="wrap breadcrumb">
  <a href="{{ route('home') }}">Home</a> / <span>{{ $page->title }}</span>
</div>

<div class="page-header reveal">
  <h1>{{ $page->title }}</h1>
</div>

<div class="wrap reveal" style="max-width:760px; margin-bottom:60px;">
  <div style="font-size:15px; color:var(--ink-soft); line-height:1.7;">{!! $page->content !!}</div>

  @if ($page->slug === 'contact')
  <div style="margin-top:40px; padding-top:32px; border-top:1px solid var(--line); font-size:14px;">
    <p style="margin-bottom:10px;"><strong>Email:</strong> <a href="mailto:{{ $siteSetting->contact_email }}" style="text-decoration:underline;">{{ $siteSetting->contact_email }}</a></p>
    @if ($siteSetting->contact_phone)
    <p style="margin-bottom:10px;"><strong>Phone:</strong> {{ $siteSetting->contact_phone }}</p>
    @endif
    @if ($siteSetting->contact_whatsapp)
    <p style="margin-bottom:10px;"><strong>WhatsApp:</strong> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSetting->contact_whatsapp) }}" target="_blank" style="text-decoration:underline;">{{ $siteSetting->contact_whatsapp }}</a></p>
    @endif
    @if ($siteSetting->address)
    <p><strong>Address:</strong> {{ $siteSetting->address }}</p>
    @endif
  </div>
  @endif
</div>

<section class="badges reveal">
  <div class="wrap badge-grid">
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-star"></i></div><p>Carefully selected, quality products</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-truck"></i></div><p>Flat-rate shipping, urban &amp; rural NZ</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><p>Secure checkout, no account needed</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-building-columns"></i></div><p>Simple manual bank transfer payment</p></div>
  </div>
</section>

@endsection
