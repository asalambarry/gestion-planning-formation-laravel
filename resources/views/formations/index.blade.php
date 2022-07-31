{{-- List --}}
@extends('layouts.auth')

@section('title', 'Liste des formations')

@section('contenu')

    @if(auth()->user()->isAdmin())
        <div class="float-right">
            <a href="{{ route('formations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter</a>
        </div>
    @endif

    <h2>
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

    @if( ! count($formations))

        <h3>Aucun item n'a été trouvé !</h3>

    @else

        {!! $formations->links() !!}

        <div class="overflow-auto">

            <table class="table table-hover table-striped table-middle">

                <thead>
                    <tr class="head-table">
                        <th class="text-success text-center">Num</th>
                        <th class="text-success">Intitulé</th>
                        <th class="text-success text-center">NB de Cours</th>
                        <th class="text-success text-center">NB d'étudiants</th>
                        <th class="text-success text-center">Opération</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($formations as $formation)

                    <tr>

                        <td class="text-center">{!! $loop->iteration !!}</td>

                        <td>
                            <a href="{{ route('formations.show', [$formation->id]) }}" title="Voir">
                                {{ ucfirst($formation->intitule) }}
                            </a>
                        </td>

                        <td class="text-center">{{ $formation->cours()->count() }}</td>

                        <td class="text-center">{{ $formation->users()->count() }}</td>

                        <td class="text-center">

                            @if(auth()->user()->isAdmin())

                                @component('components.update-buttons', [
                                   'id' => $formation->id,
                                   'edit_route' => 'formations.edit',
                                   'destroy_route' => 'formations.destroy',
                                   'name' => 'formation',
                                   'buttons' => false,
                                   'back_button' => false,
                                   'others' => '',
                                ])
                                @endcomponent

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        {!! $formations->links() !!}

    @endif

@endsection
