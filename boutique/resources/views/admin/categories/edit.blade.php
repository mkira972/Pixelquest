@extends('layouts.admin')
@section('title', 'Modifier : ' . $category->name)

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @include('admin.categories._form')
    </form>
@endsection
