{{-- List --}}
@extends('layouts.auth')

@section('title', 'Liste des plannings')

@section('contenu')

    @if( ! auth()->user()->isEtudiant())
        <div class="float-right">
            <a href="{{ route('plannings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter</a>
        </div>
    @endif

    <h2 class="m-4">
		{!! $title !!}
	</h2>

	<!-- Zone d'information -->
    @component('components.alert', [])
    @endcomponent

    @include('plannings.planning_list')

@endsection
