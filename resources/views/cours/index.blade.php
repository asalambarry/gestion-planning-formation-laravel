{{-- List --}}
@extends('layouts.auth')

@section('title', 'Liste des cours')

@section('contenu')

    @if(auth()->user()->isAdmin())

        <div class="float-right">
            <a href="{{ route('cours.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter</a>
        </div>

    @endif

    @isset($enseignants)

        <form action="{{ url('cours-par-enseignant') }}">

            <label> Choisissez un enseignant

                <select name="login" onchange="this.form.submit()" class="p-2">

                    <option value="tous">Tous</option>

                    @foreach($enseignants as $key => $val)
                        <option @if ($login == $key) selected @endif value="{{ $key }}">{{ $val }}</option>
                    @endforeach

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

    @if( ! count($cours))

        <h3>Aucun item n'a été trouvé !</h3>

    @else

        {!! $cours->withQueryString()->links() !!}

        <div class="overflow-auto">

            <table class="table table-hover table-striped table-middle">

                <thead>
                    <tr class="head-table">
                        <th class="text-success text-center">Num</th>
                        <th class="text-success">Intitulé</th>
                        <th class="text-success">Enseignant</th>
                        <th class="text-success text-center">Opération</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($cours as $cour)

                    <tr>

                        <td class="text-center">{!! $loop->iteration !!}</td>

                        <td>
                            <a href="{{ route('cours.show', [$cour->id]) }}" title="Voir tous les détails">
                                {{ ucfirst($cour->intitule) }}
                            </a>
                        </td>

                        <td>
                            @if($cour->enseignant)
                                <a href="{{ route('users.show', [$cour->enseignant->id])}}" title="Voir">
                                    {{ ucfirst($cour->enseignant->nom) }} {{ ucfirst($cour->enseignant->prenom) }}
                                </a>
                            @endif
                        </td>

                        <td class="text-center">

                            @if(auth()->user()->isAdmin())

                                @component('components.update-buttons', [
                                   'id' => $cour->id,
                                   'edit_route' => 'cours.edit',
                                   'destroy_route' => 'cours.destroy',
                                   'name' => 'cour',
                                   'buttons' => false,
                                   'back_button' => false,
                                   'others' => '',
                                ])
                                @endcomponent

                            @elseif(auth()->user()->isEtudiant())

                                @if(auth()->user()->cours()->where('id', $cour->id)->count())

                                    <a href="javascript:void(0)" class="btn btn-danger btn-lg"
                                       title="Desinscription"
                                       onclick="if(confirm('Souhaitez-vous vraiment vous désinscrire ?'))
                                           document.querySelector('#desinscription-{!! $cour->id !!}').submit();">
                                        Se Désinscrire
                                    </a>

                                    <form id="desinscription-{!! $cour->id !!}" action="{!! route('desinscription', [$cour->id]) !!}" method="POST">
                                        @csrf
                                    </form>

                                @else

                                    <a class="btn btn-secondary btn-lg" href="{!! url('inscription-cours/'.$cour->id) !!}" title="S'inscrire">
                                        S'inscrire
                                    </a>

                                @endif

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>
        {!! $cours->withQueryString()->links() !!}
    @endif
@endsection
