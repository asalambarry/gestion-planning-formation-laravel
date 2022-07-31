
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Icone-->
    <link rel="icon" href="{!! asset('favicon.png') !!}" type="image/png" sizes="16x16">

    <title>Connexion</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{!! asset('css/bootstrap.css') !!}" />
    <link href="{!! asset('css/signin.css') !!}" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body class="text-center">

<form class="form-signin" method="post" action="{{ route('login') }}">

    @csrf
    <img class="mb-4" src="{{ asset('profil.png') }}" alt="icon" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">Connectez-vous !</h1>

    @error('login')
    <p class="text-danger">
        <span class="" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    </p>
    @enderror

    @component('components.alert', [])
    @endcomponent

    <label for="login" class="sr-only">Login</label>
    <input name="login" type="text" id="login" value="{{ old('login') }}" class="form-control" placeholder="Login" required autofocus>

    <label for="mdp" class="sr-only">Mot de passe</label>
    <input name="mdp" type="password" id="mdp" class="form-control" placeholder="Password" required>

    <div class="checkbox mb-3">
        <label>
            <input name="remember_me" type="checkbox" value="remember-me"> Se souvenir de moi
        </label>
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
    <p>
        <a class="nav-link" href="{{ route('register') }}">
            {{ __('Creer un compte') }}
        </a>
    </p>

    <p class="mt-5 mb-3 text-muted">&copy; Plannings des cours - 2021</p>
</form>
</body>
</html>
