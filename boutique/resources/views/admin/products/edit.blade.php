@extends('layouts.admin')
@section('title', 'Modifier : ' . $product->name)

@section('content')
    {{-- enctype est OBLIGATOIRE pour envoyer un fichier. Sans lui le
         navigateur n'envoie que le nom du fichier, pas son contenu. --}}
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @include('admin.products._form')
    </form>
@endsection
