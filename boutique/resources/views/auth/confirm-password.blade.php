@extends('layouts.guest')
@section('title', 'Confirmation')
@section('subtitle', 'Zone securisee')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required autofocus>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-pq w-100">Confirmer</button>
    </form>
@endsection
