@extends('layouts.admin')
@section('title', 'Modifier : ' . $user->name)

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @include('admin.users._form')
    </form>
@endsection
