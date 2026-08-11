@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page_title', 'Edit Category — '.$category->name)

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.categories._form', ['category' => $category])
        </form>
    </div>
</div>
@endsection
