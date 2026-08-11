@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product — '.$product->name)

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form', ['product' => $product])
        </form>
    </div>
</div>

@include('admin.products._variants', ['product' => $product])
@endsection
