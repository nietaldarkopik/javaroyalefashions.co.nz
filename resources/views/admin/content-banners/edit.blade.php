@extends('layouts.admin')

@section('title', 'Edit Content Banner')
@section('page_title', 'Edit Content Banner — '.($banner->heading ?: 'Untitled'))

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.content-banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.content-banners._form', ['banner' => $banner])
        </form>
    </div>
</div>
@endsection
