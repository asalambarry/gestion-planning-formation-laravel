
{{-- Edit form --}}

@extends('layouts.auth')

@section('title', 'Mise à jour du planning')

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Modification du planning
				</h3>

                @component('plannings.form_create', [
                    'url' => route('plannings.update', $planning->id),
                    'method' => 1,
                    'cours_id' => $planning->cours_id,
                    'date_debut' => $planning->date_debut,
                    'date_fin' => $planning->date_fin,

                    'cours' => $cours,
                ]);

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
