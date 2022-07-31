
{{-- Page Showing --}}

@extends('layouts.auth')

@section('title', ucfirst($formation->intitule))

@section('contenu')

    <div class="card card-body">

        <h3 class="card-title">
            <span class="text-primary">{{ ucfirst($formation->intitule) }}</span>
        </h3>

        <hr>

        <div class="row">

            <p class="col-md-4 text-center">
                <i class="fa fa-image fa-4x"></i>
            </p>

            <div class="col-md-8">

                <p>
                    <strong>Intitulé</strong> : {{ ucfirst($formation->intitule) }}
                </p>

                <hr/>

                <h5>Tous les cours de cette formation</h5>

                @if( ! $formation->cours()->count())

                    <strong>Aucun cours !</strong>

                @else

                    <div class="overflow-auto">

                        <table class="table table-hover table-striped table-middle">

                            <thead>

                                <tr class="head-table">

                                    <th class="text-success text-center">Num</th>

                                    <th class="text-success text-center">Intitulé</th>

                                    <th class="text-success text-center">Enseignant</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($formation->cours as $cour)

                                    <tr class="text-center">

                                        <td>{!! $loop->iteration !!}</td>

                                        <td>{{ ucfirst($cour->intitule) }}</td>

                                        <td>{{ ucfirst($cour->enseignant->nom).' '.ucfirst($cour->enseignant->prenom) }}</td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif

                @if(auth()->user()->isAdmin())

                    <hr/>

                    <h5>Etudiants inscrits dans cette formation</h5>

                    @if( ! $formation->users)

                        <strong>Aucun étudiant !</strong>

                    @else

                        <div class="overflow-auto">

                            <table class="table table-hover table-striped table-middle">

                                <thead>

                                    <tr class="head-table">

                                        <th class="text-success text-center">Num</th>

                                        <th class="text-success text-center">Login</th>

                                        <th class="text-success text-center">Nom</th>

                                        <th class="text-success text-center">Prénom</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($formation->users as $user)

                                        <tr class="text-center">

                                            <td>{!! $loop->iteration !!}</td>

                                            <td>
                                                <a href="{{ route('users.show', [$user->id]) }}" title="Voir">
                                                    {{ $user->login }}
                                                </a>
                                            </td>

                                            <td>{{ ucfirst($user->nom) }}</td>

                                            <td>{{ ucfirst($user->prenom) }}</td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @endif

                @endif

                <hr class="divider">

                <div class="text-right">

                    @if(auth()->user()->isAdmin())

                        @component('components.update-buttons', [
                            'id' => $formation->id,
                            'edit_route' => 'formations.edit',
                            'destroy_route' => 'formations.destroy',
                            'name' => 'formation',
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
