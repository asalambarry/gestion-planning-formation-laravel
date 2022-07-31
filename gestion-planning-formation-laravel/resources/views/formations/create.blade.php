{{-- Create a formation --}}

@extends('layouts.auth')

@section('title', 'Enregistrer une formation')

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Nouvelle Formation
				</h3>

                @component('formations.form_create', [
                    'url' => route('formations.store'),
                    'method' => 0,

                ])

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
