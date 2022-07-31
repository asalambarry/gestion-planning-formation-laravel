<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	@section('metas')
		<meta name="description" content="Planning des cours">
		<meta name="author" content="{!! config('app.name') !!}">
	@show

    <title>@yield('title') - {!! config('app.name') !!}</title>

    <!-- Icone-->
    <link rel="icon" href="{!! asset('favicon.png') !!}" type="image/png" sizes="16x16">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Les feuilles de styles -->
    @section('styles')

        <link rel="stylesheet" href="{!! asset('css/bootstrap.css') !!}" />
        <link href="{!! asset('css/main.css') !!}" rel="stylesheet">

    @show

</head>
<body>

    <!-- L'entete de la page -->
    @include('head')

    <!-- La partie centrale -->
    <main class="container" role="main">

        @yield('content')

    </main>

    <!-- Le pied de page -->
    <footer class="container-fluid">
        <p class="text-right m-md-2">
            <a href="#">Revenir en haut</a>
        </p>
        <p class="text-center mt-5 mb-3 text-muted">&copy; Plannings - 2021</p>
    </footer>

    <!-- Insertion des scripts -->
    @section('scripts')
        <script src="{!! asset('js/jquery-3.5.1.slim.min.js') !!}"></script>
        <script src="{!! asset('js/bootstrap.bundle.js') !!}"></script>
        <script src="{!! asset('js/main.js') !!}"></script>
    @show

</body>
</html>
