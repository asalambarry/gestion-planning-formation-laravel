@extends('layouts.app')

@section('title', 'Creation de compte')

@section('content')

    <form class="form-signin" style="width: 400px; margin: 5px auto 40px;" method="post" action="{{ route('register') }}">

        @csrf

        <p class="text-center mb-4">
            <img style="text-align: center; width: 72px" src="{{ asset('profil.png') }}" alt="logo" width="72" height="72">
        </p>

        <h1 class="h3 mb-3 font-weight-normal text-center">Inscription</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="nom">Nom</label>
            <input name="nom" type="text" class="form-control" value="{{ old('nom') }}" id="nom" placeholder="Nom">
        </div>

        <div class="form-group">
            <label for="prenom">Prenom</label>
            <input name="prenom" type="text" class="form-control" value="{{ old('prenom') }}" id="prenom" placeholder="Prenom">
        </div>

        <div class="form-group">
            <label for="login">Login</label>
            <input name="login" type="text" class="form-control"  value="{{ old('login') }}" id="login" placeholder="login">
        </div>

        <div class="form-group">
            <label for="mdp">Mot de passe</label>
            <input name="mdp" type="password" class="form-control" id="mdp" placeholder="mot de passe">
        </div>

        @component('components.select', [
            'name' => 'formation_id',
            'value' => old('formation_id') ?? ($formation_id ?? ''),
            'optional' => true,
            'label' => 'Formation',
            'values' => $formations,
            'adds' => [],
            'others' => '',
        ])
        @endcomponent

        <button class="btn btn-lg btn-primary btn-block" type="submit">S'inscrire</button>

        <p class="text-center">
            <a class="nav-link" href="{{ route('login') }}">
                {{ __('Se connecter') }}
            </a>
        </p>

    </form>

@endsection
