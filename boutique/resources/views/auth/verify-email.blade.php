@extends('layouts.guest')
@section('title', 'Verification de l\'e-mail')
@section('subtitle', 'Verifiez votre adresse e-mail')

@section('content')
    @if(session('status') === 'verification-link-sent')
        <div class="alert alert-success">Un nouveau lien de verification vient d'etre envoye.</div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">
            @csrf
            <button type="submit" class="btn btn-pq w-100">Renvoyer l'e-mail</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Deconnexion</button>
        </form>
    </div>
@endsection
