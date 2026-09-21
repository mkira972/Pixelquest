@extends('layouts.guest')
@section('title', 'Connexion')
@section('subtitle', 'Content de vous revoir !')

@section('content')
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn btn-pq w-100">Se connecter</button>

        <div class="d-flex justify-content-between mt-3 small">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublie ?</a>
            @endif
            <a href="{{ route('register') }}">Creer un compte</a>
        </div>
    </form>
@endsection
