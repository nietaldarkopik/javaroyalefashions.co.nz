@extends('layouts.admin')

@section('title', 'Edit Hero Slide')
@section('page_title', 'Edit Hero Slide — '.$heroSlide->heading)

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hero-slides.update', $heroSlide) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.hero-slides._form', ['heroSlide' => $heroSlide])
        </form>
    </div>
</div>
@endsection
