@extends('layouts.admin')

@section('title', 'New Hero Slide')
@section('page_title', 'New Hero Slide')

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.hero-slides._form')
        </form>
    </div>
</div>
@endsection
