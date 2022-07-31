{{-- List --}}
@extends('layouts.auth')

@section('title', 'Liste des utilisateurs')

@section('contenu')

    @if(auth()->user()->isAdmin())

        <div class="float-right">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter</a>
        </div>

    @endif

    @isset($typeExists)

        <form action="{{ route('users.index') }}">

            <label> Affichage

                <select name="type" onchange="this.form.submit()" class="p-2">
                    <option value="tous" @if($type == 'tous') selected @endif>Tous</option>
                    <option value="etudiant" @if($type == 'etudiant') selected @endif>Etudiant</option>
                    <option value="enseignant" @if($type == 'enseignant') selected @endif>Enseignant</option>
                    <option value="admin" @if($type == 'admin') selected @endif>Admin</option>
                    <option value="auto-crees" @if($type == 'auto-crees') selected @endif>Auto-crées</option>
                </select>

            </label>

        </form>

    @endisset

    <h2 class="m-4">
		{!! $title !!}
	</h2>

    @if(isset($info))

        <div class="alert alert-warning alert-dismissible fade show" role="alert">

            <p>
				{{ $info }}
            </p>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>

    @endif

	<!-- Zone d'information -->
    @component('components.alert', [])
    @endcomponent

    @if( ! count($users))

        <h3>Aucun item n'a été trouvé !</h3>

    @else

        {!! $users->withQueryString()->links() !!}

        <div class="overflow-auto">

            <table class="table table-hover table-striped table-middle">

                <thead>
                    <tr class="head-table">
                        <th class="text-success text-center">Num</th>
                        <th class="text-success">Nom/Prénom</th>
                        <th class="text-success">Login</th>
                        <th class="text-success text-center">Type</th>
                        <th class="text-success text-center">Opération</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($users as $user)

                    <tr>

                        <td class="text-center">{!! $loop->iteration !!}</td>

                        <td>
                            <a href="{{ route('users.show', [$user->id]) }}" title="Voir tous les détails">
                                {{ ucfirst($user->nom) }} {{ ucfirst($user->prenom) }}
                            </a>
                        </td>

                        <td>{{ $user->login }}</td>

                        <td class="text-center">{{ ucfirst($user->type) }}</td>

                        <td class="text-center">

                            @if(auth()->user()->isAdmin() and isset($type) and $type === 'auto-crees')

                                <a class="btn btn-link" href="{!! route('users.edit', [$user->id]) !!}" title="Modifier">
                                    Accepter
                                </a>

                                <a class="btn btn-danger btn-small" href="javascript:void(0)" title="Réfuser"
                                   onclick="if(confirm('Confirmer ?')) document.querySelector('#refuser-inscription-{!! $user->id !!}').submit();">
                                    Réfuser
                                </a>

                                <form id="refuser-inscription-{!! $user->id !!}" action="{!! route('refuserInscription', [$user->id]) !!}" method="POST">
                                    @csrf
                                </form>

                            @elseif(auth()->user()->isAdmin() or auth()->id() === $user->id)

                                @component('components.update-buttons', [
                                   'id' => $user->id,
                                   'edit_route' => 'users.edit',
                                   'destroy_route' => 'users.destroy',
                                   'name' => 'utilisateur',
                                   'buttons' => false,
                                   'back_button' => false,
                                ])
                                @endcomponent

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        {!! $users->withQueryString()->links() !!}

    @endif

@endsection
