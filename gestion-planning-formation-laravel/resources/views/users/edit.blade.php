
{{-- Edit form --}}

@extends('layouts.auth')

@section('title', 'Mise à jour de '.ucfirst($user->nom))

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Modification de {{ ucfirst($user->nom) }}
				</h3>

                @component('users.form_create', [
                    'url' => route('users.update', $user->id),
                    'method' => 1,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'login' => $user->login,
                    'mdp' => $user->mdp,
                    'formation_id' => $user->formation_id,
                    'type' => $user->type,

                    'formations' => $formations,
                ]);

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
