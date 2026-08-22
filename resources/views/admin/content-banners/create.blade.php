@extends('layouts.admin')

@section('title', 'New Content Banner')
@section('page_title', 'New Content Banner')

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.content-banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.content-banners._form')
        </form>
    </div>
</div>
@endsection
