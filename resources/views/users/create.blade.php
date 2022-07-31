{{-- Create a user --}}

@extends('layouts.auth')

@section('title', 'Enregistrer un utilisateur')

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Nouvel utilisateur
				</h3>

                @component('users.form_create', [
                    'url' => route('users.store'),
                    'method' => 0,

                    'formations' => $formations,
                ])

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
