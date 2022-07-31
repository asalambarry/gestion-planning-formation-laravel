{{-- auth layout --}}

@extends('layouts.app')

@section('title', 'Espace personnel !')

@section('content')

    <div class="container-fluid">

        <div class="row">

            <nav id="sidebarMenu" class="col-md-4 col-lg-3 d-md-block bg-light sidebar collapse">

                <div class="sidebar-sticky pt-3">

                    <ul class="nav flex-column">

                        <li class="nav-item">
                            <span data-feather="home"></span>
                                Menu <span class="sr-only">(current)</span>
                            <hr/>
                        </li>

                        @if(auth()->user()->isEtudiant())

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('ma-formation') }}">
                                    <span data-feather="file"></span>
                                    Ma formation
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mes-cours?formation=true') }}">
                                    <span data-feather="file"></span>
                                    Cours de ma formation
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cours.index') }}">
                                    <span data-feather="file"></span>
                                    S'inscrire dans un cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mes-cours') }}">
                                    <span data-feather="file"></span>
                                    Se desinscrire d'un cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mes-cours') }}">
                                    <span data-feather="file"></span>
                                    Mes cours
                                </a>
                                <hr/>
                            </li>

                            <li class="nav-item">
                                Rechercher un cours
                                <form action="{{ url('recherche-cours') }}" method="get" >
                                    <input type="text" name="q" value="{{ old('q') }}" required style="width: 150px"
                                    placeholder="cours"/>
                                    <input type="submit" value="Chercher">
                                </form>
                                <hr/>
                            </li>

                        @elseif(auth()->user()->isEnseignant())

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mes-cours') }}">
                                    <span data-feather="file"></span>
                                    Mes cours
                                </a>
                                <hr/>
                            </li>

                        @elseif(auth()->user()->isAdmin())

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <span data-feather="file"></span>
                                    Liste des utilisateurs
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.create') }}">
                                    <span data-feather="users"></span>
                                    Nouvel utilisateur
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <span data-feather="users"></span>
                                    Modifier, Supprimer un utilisateur
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}?type=auto-crees">
                                    <span data-feather="users"></span>
                                    Utilisateurs auto-crées
                                </a>
                            </li>

                            <li class="nav-item">
                                Rechercher un utilisateur
                                <form action="{{ url('recherche-utilisateur') }}" method="get" >
                                    <input type="text" name="q" value="{{ old('q') }}" required style="width: 150px"
                                           placeholder="nom, prénom ou login"/>
                                    <input type="submit" value="Chercher">
                                </form>
                                <hr/>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cours.index') }}">
                                    <span data-feather="file"></span>
                                    Liste des cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cours.create') }}">
                                    <span data-feather="file"></span>
                                    Nouveau cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cours.index') }}">
                                    <span data-feather="file"></span>
                                    Modifier, supprimer un cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('cours-par-enseignant') }}">
                                    <span data-feather="file"></span>
                                    Liste des cours par enseignant
                                </a>
                            </li>

                            <li class="nav-item">
                                Rechercher un cours
                                <form action="{{ url('recherche-cours') }}" method="get" >
                                    <input type="text" name="q" value="{{ old('q') }}" required style="width: 150px"
                                    placeholder="intitulé"/>
                                    <input type="submit" value="Chercher">
                                </form>
                                <hr/>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('formations.index') }}">
                                    <span data-feather="file"></span>
                                    Liste des formations
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('formations.create') }}">
                                    <span data-feather="file"></span>
                                    Nouvelle formation
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('formations.index') }}">
                                    <span data-feather="file"></span>
                                    Modifier, supprimer une formation
                                </a>
                                <hr/>
                            </li>

                        @endif

                        @if(auth()->user()->isEtudiant() or auth()->user()->isEnseignant())

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mon-planning?type=integral') }}">
                                    <span data-feather="file"></span>
                                    Mon Planning intégral
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mon-planning?type=cours') }}">
                                    <span data-feather="file"></span>
                                    Mon Planning par cours
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('mon-planning?type=semaine') }}">
                                    <span data-feather="file"></span>
                                    Mon Planning par semaine
                                </a>
                                <hr/>
                            </li>

                        @endif

                        @if(auth()->user()->isAdmin() or auth()->user()->isEnseignant())

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plannings.index') }}">
                                    <span data-feather="file"></span>
                                    Liste des plannings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plannings.create') }}">
                                    <span data-feather="file"></span>
                                    Nouveau planning
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plannings.index') }}">
                                    <span data-feather="file"></span>
                                    Modifier, supprimer un planning
                                </a>
                                <hr/>
                            </li>

                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.show', [auth()->user()->id]) }}">
                                <span data-feather="users"></span>
                                Voir mon compte
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.edit', [auth()->user()->id]) }}">
                                <span data-feather="users"></span>
                                Modifier mon compte
                            </a>
                            <hr/>
                        </li>

                    </ul>

                </div>

            </nav>

            <main role="main" class="col-md-8 col-lg-9 ml-sm-auto pt-3 pb-3">

                @yield('contenu')

            </main>

        </div>

    </div>

@endsection
