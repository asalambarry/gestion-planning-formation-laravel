
{{-- Page Showing --}}

@extends('layouts.auth')

@section('title', ucfirst($cour->intitule))

@section('contenu')

    <div class="card card-body">

        @component('components.alert', [])
        @endcomponent

        <h3 class="card-title">
            <span class="text-primary">Cours: {{ ucfirst($cour->intitule) }}</span>
        </h3>

        <hr>

        <div class="row">

            <p class="col-md-4 text-center">
                <i class="fa fa-image fa-4x"></i>
            </p>

            <div class="col-md-8">

                <p>
                    <strong>Intitule</strong> : {{ ucfirst($cour->intitule) }}
                </p>

                <hr class="divider">
                <p>
                    <strong>Enseignant</strong> :

                    @if($cour->enseignant)
                        <a href="{{ route('users.show', [$cour->enseignant->id])}}" title="Voir cet élément">
                            {{ ucfirst($cour->enseignant->nom) }} {{ ucfirst($cour->enseignant->prenom) }}
                        </a>
                    @endif

                </p>

                @if($cour->formation)

                    <hr class="divider">

                    <p>
                        <strong>Ce cours fait partir de la formation</strong> :

                        <a href="{{ route('formations.show', [$cour->formation->id])}}" title="Voir cet élément">
                            {{ ucfirst($cour->formation->intitule) }}
                        </a>
                    </p>

                @endif

                <hr class="divider">

                <strong class="mt-4">Plannings</strong>

                @if($cour->plannings->count())

                    @include('cours.planning-cours')

                @endif

                <hr class="divider">

                <div class="text-right">

                    @if(auth()->user()->isEtudiant())

                        @if(auth()->user()->cours()->where('id', $cour->id)->count())

                            <a href="javascript:void(0)" class="btn btn-danger btn-lg"
                               title="Desinscription"
                               onclick="if(confirm('Souhaitez-vous vraiment vous désinscrire ?'))
                                   document.querySelector('#desinscription-{!! $cour->id !!}').submit();">
                                Se Désinscrire
                            </a>

                        @else

                            <a class="btn btn-secondary btn-lg" href="{!! url('inscription-cours/'.$cour->id) !!}" title="S'inscrire">
                                S'inscrire a ce cours
                            </a>

                        @endif

                    @elseif(auth()->user()->isAdmin())

                        @component('components.update-buttons', [
                            'id' => $cour->id,
                            'edit_route' => 'cours.edit',
                            'destroy_route' => 'cours.destroy',
                            'name' => 'cour',
                            'buttons' => true,
                            'back_button' => false,
                            'others' => '',
                        ])
                        @endcomponent

                    @endif

                    <form id="desinscription-{!! $cour->id !!}" action="{!! route('desinscription', [$cour->id]) !!}" method="POST">
                        @csrf
                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection
