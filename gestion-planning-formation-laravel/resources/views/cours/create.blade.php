{{-- Create a cour --}}

@extends('layouts.auth')

@section('title', 'Enregistrer un cours')

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Nouveau Cours
				</h3>

                @component('cours.form_create', [
                    'url' => route('cours.store'),
                    'method' => 0,

                    'users' => $users,
                    'formations' => $formations,
                ])

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
