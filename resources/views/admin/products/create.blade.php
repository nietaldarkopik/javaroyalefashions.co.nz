@extends('layouts.admin')

@section('title', 'New Product')
@section('page_title', 'New Product')

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</div>
@endsection
