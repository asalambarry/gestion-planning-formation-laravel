{{-- Create a planning --}}

@extends('layouts.auth')

@section('title', 'Enregistrer un planning')

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Nouveau Planning
				</h3>

                @component('plannings.form_create', [
                    'url' => route('plannings.store'),
                    'method' => 0,

                    'cours' => $cours,
                ])

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
