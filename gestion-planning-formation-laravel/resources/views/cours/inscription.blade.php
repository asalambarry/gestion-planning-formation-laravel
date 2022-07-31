
@extends('layouts.auth')

@section('title', 'Inscription au cours: '.ucfirst($cour->intitule))

@section('contenu')

    <div class="card card-body">

        <h3 class="card-title">
            <span class="text-primary">Inscription au cours <strong>&#171; {{ ucfirst($cour->intitule) }} &#187;</strong></span>
        </h3>

        <h4 class="mt-4">Tous les plannings</h4>

        @include('cours.planning-cours')

        <form action="{!! url('inscription-cours') !!}" method="POST" class="mt-4 mb-4">

            @csrf

            <div>Voulez-vous vous inscrire a ce cours ?

                <input type="hidden" name="cours_id" value="{!! $cour->id !!}" />

                <input type="submit" value="S'inscrire" class="btn btn-primary btn-lg" />

            </div>

        </form>

    </div>

@endsection


