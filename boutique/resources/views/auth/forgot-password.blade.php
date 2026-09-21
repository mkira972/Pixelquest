@extends('layouts.guest')
@section('title', 'Mot de passe oublie')
@section('subtitle', 'Reinitialisation du mot de passe')

@section('content')
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-pq w-100">Envoyer le lien de reinitialisation</button>

        <p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}">Retour a la connexion</a></p>
    </form>
@endsection
