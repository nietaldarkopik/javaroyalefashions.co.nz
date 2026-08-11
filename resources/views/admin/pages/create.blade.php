@extends('layouts.admin')

@section('title', 'New Page')
@section('page_title', 'New Page')

@section('main_content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf
            @include('admin.pages._form')
        </form>
    </div>
</div>
@endsection
