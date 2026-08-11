@extends('adminlte::page')

{{-- Explicitly built here (not left to config('adminlte.title')) because
     this section, once set, always wins over the config-based @yield
     fallback in adminlte::master's <title> tag — so the dynamic site name
     has to be spliced in at this level to actually show up. --}}
@section('title', ($title ?? 'Dashboard').' — '.($setting->site_name ?? 'Admin'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>@yield('page_title')</h1>
        @hasSection('page_actions')
        <div>@yield('page_actions')</div>
        @endif
    </div>
@stop

@section('content')
    @if (session('status'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- NOTE: intentionally NOT named 'body' — that section name is used
         internally by adminlte::page/master for the entire <body> wrapper
         (navbar+sidebar+content+footer). Reusing it here overwrites that
         wrapper with just this page's content, which is exactly the "no
         navigation" bug this comment is here to stop someone reintroducing. --}}
    @yield('main_content')
@stop

@section('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @if ($setting->favicon_path ?? false)
    <link rel="icon" href="{{ Storage::disk('public')->url($setting->favicon_path) }}">
    @endif
    @yield('extra_css')
@stop

@section('js')
    @yield('extra_js')
@stop
