
{{-- Page Showing --}}

@extends('layouts.auth')

@section('title', 'Planning du cours '.ucfirst($planning->cours->intitule))

@section('contenu')

    <div class="card card-body">

        <h3 class="card-title">
            <span class="text-primary">Planning du cours <strong>&#171; {{ ucfirst($planning->cours->intitule) }} &#187;</strong></span>
        </h3>

        <hr>

        <div class="row">

            <p class="col-md-4 text-center">
                <i class="fa fa-image fa-4x"></i>
            </p>

            <div class="col-md-8">

                <p>
                    <strong>Cours</strong> :
                    @if($planning->cours)
                        <a href="{{ route('cours.show', [$planning->cours->id])}}" title="Voir">
                            {{ ucfirst($planning->cours->intitule) }}
                        </a>
                    @endif
                </p>

                <hr class="divider">
                <p>
                    <strong>Date de début</strong> : {{ optional($planning->date_debut)->format('d/m/Y H:i') }}
                </p>

                <p>
                    <strong>Date de fin</strong> : {{ optional($planning->date_fin)->format('d/m/Y H:i') }}
                </p>

                <hr class="divider">

                <div class="text-right">

                    @if( ! auth()->user()->isEtudiant())

                        @component('components.update-buttons', [
                            'id' => $planning->id,
                            'edit_route' => 'plannings.edit',
                            'destroy_route' => 'plannings.destroy',
                            'name' => 'planning',
                            'buttons' => true,
                            'back_button' => false,
                            'others' => '',
                        ])
                        @endcomponent

                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection
