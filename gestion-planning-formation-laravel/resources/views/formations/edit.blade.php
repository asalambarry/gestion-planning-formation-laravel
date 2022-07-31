
{{-- Edit form --}}

@extends('layouts.auth')

@section('title', 'Mise à jour de '.$formation->intitule)

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Modification de {{ ucfirst($formation->intitule) }}
				</h3>

                @component('formations.form_create', [
                    'url' => route('formations.update', $formation->id),
                    'method' => 1,
                    'intitule' => $formation->intitule,

                ]);

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
