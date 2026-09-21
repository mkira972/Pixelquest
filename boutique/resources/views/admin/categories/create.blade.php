@extends('layouts.admin')
@section('title', 'Nouvelle categorie')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @include('admin.categories._form')
    </form>
@endsection
