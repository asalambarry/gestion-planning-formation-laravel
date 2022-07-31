
{{-- Edit form --}}

@extends('layouts.auth')

@section('title', 'Mise à jour de '.$cour->intitule)

@section('contenu')

    <div class="row">

        <div class="col-md">

            <div class="card card-body shadow-sm">

                <h3 class="card-title text-primary">
					Modification de {{ ucfirst($cour->intitule) }}
				</h3>

                @component('cours.form_create', [
                    'url' => route('cours.update', $cour->id),
                    'method' => 1,
                    'intitule' => $cour->intitule,
                    'user_id' => $cour->user_id,
                    'formation_id' => $cour->formation_id,

                    'users' => $users,
                    'formations' => $formations,
                ]);

                    <strong>Oop's!</strong> Erreur !

                @endcomponent

            </div>

        </div>

    </div>

@endsection
