@extends('layouts.admin')

@section('title', 'New Category')
@section('page_title', 'New Category')

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
