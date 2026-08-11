@extends('layouts.admin')

@section('title', 'Edit Page')
@section('page_title', 'Edit Page — '.$page->title)

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.pages._form', ['page' => $page])
        </form>
    </div>
</div>
@endsection
