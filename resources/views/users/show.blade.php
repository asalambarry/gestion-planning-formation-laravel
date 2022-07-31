
{{-- Page Showing --}}

@extends('layouts.auth')

@section('title', ucfirst($user->nom) . ' ' . ucfirst($user->prenom))

@section('contenu')

    <div class="card card-body">

        <h3 class="card-title">
            <span class="text-primary">{{ ucfirst($user->nom) . ' ' . ucfirst($user->prenom) }}</span>
        </h3>

        <hr>

        <div class="row">

            <p class="col-md-4 text-center">
                <i class="fa fa-image fa-4x"></i>
            </p>

            <div class="col-md-8">

                <p>
                    <strong>Nom</strong> : {{ ucfirst($user->nom) }}
                </p>

                <hr class="divider">
                <p>
                    <strong>Prenom</strong> : {{ ucfirst($user->prenom) }}
                </p>

                <hr class="divider">
                <p>
                    <strong>Login</strong> : {{ $user->login }}
                </p>

                <hr class="divider">
                <p>
                    <strong>Type</strong> : {{ ucfirst($user->type) }}
                </p>

                <hr class="divider">
                <p>
                    <strong>Formation</strong> :
                    @if($user->formation)
                        <a href="{{ route('formations.show', [$user->formation->id])}}" title="Voir">
                            {{ ucfirst($user->formation->intitule) }}
                        </a>
                    @endif
                </p>

                <hr class="divider">

                <div class="text-right">

                    @if(auth()->user()->isAdmin() or auth()->id() === $user->id)

                        @component('components.update-buttons', [
                            'id' => $user->id,
                            'edit_route' => 'users.edit',
                            'destroy_route' => 'users.destroy',
                            'name' => 'user',
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
