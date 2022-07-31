{{-- Entete --}}

<nav class="navbar navbar-expand-xl navbar-light bg-light">

    <a href="{{ url('/') }}" class="navbar-brand" style="padding-right: 0">
        <img src="{!! asset('favicon.png') !!}" width="48" alt="logo"/>
    </a>

    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collection of nav links, forms, and other content for toggling -->
    <div id="navbarCollapse" class="collapse navbar-collapse justify-content-start">

        <div class="navbar-nav">
            @guest
                <a href="{{ url('/') }}" class="nav-item nav-link active">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Accueil
                </a>
            @else
                <a href="{{ url('/home') }}" class="nav-item nav-link active">Tableau de bord</a>
            @endguest
        </div>

        <div class="navbar-nav ml-auto">

            @guest

                <ul class="navbar-nav px-3">

                    <li class="nav-item mr-2 text-nowrap">
                        <a class="btn btn-outline-success" href="{{ route('login') }}">
                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                            {{ __('Connexion') }}
                        </a>
                    </li>

                    <li class="nav-item text-nowrap">
                        <a class="btn btn-info" href="{{ route('register') }}">
                            {{ __('Inscription') }}
                            <img src="{{ asset('0116-user-plus.png') }}" alt="img" width="26">
                        </a>
                    </li>

                </ul>

            @else

                <p class="nav-item text-primary pt-2">
                    Compte: {{ ucfirst(array_search (auth()->user()->type, config('user_role'))) }}
                </p>

                @if(auth()->user()->isEtudiant() or auth()->user()->isEnseignant())

                    <a href="{{ url('mes-cours') }}" class="nav-item nav-link messages ml-2" title="Mes cours">

                        <i class="fas fa-book-open"></i>

                        <span class="badge">

                            @if(auth()->user()->isEtudiant())
                                {{ auth()->user()->cours()->count() }}
                            @else
                                {{ auth()->user()->coursEnseignant()->count() }}
                            @endif

                        </span>

                    </a>

                @endif

                <div class="nav-item dropdown">

                    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle user-action">
                        <img src="{{ asset('profil.png') }}" class="avatar" alt="Avatar">
                        {{ ucfirst(Auth::user()->nom) }} {{ ucfirst(Auth::user()->prenom) }}
                        <b class="caret"></b>
                    </a>

                    <div class="dropdown-menu">

                        <a href="{{ url('home') }}" class="dropdown-item">
                            <i class="fa fa-user-o"></i> Dashboard</a>

                        <a href="{{ url('users/'.auth()->user()->id) }}" class="dropdown-item">
                            <i class="fa fa-sliders"></i> Mon compte</a>

                        <div class="dropdown-divider"></div>

                        <a href="javascript:void(0)" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                            {{ __('Déconnexion') }}
                        </a>
                    </div>
                </div>

            @endguest

        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
