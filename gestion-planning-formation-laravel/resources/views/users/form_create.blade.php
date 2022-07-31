{{-- Form --}}
@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

@component('components.info', [])
@endcomponent

@component('components.alert', [])
@endcomponent

<form method="POST" action="{{ $url }}" id="form_create" enctype="multipart/form-data">

    @if($method == 1)
        @method('PUT')
    @endif

    @csrf

    @component('components.input', [
        'type' => 'text',
        'name' => 'nom',
        'value' => old('nom') ?? ($nom ?? ''),
        'optional' => true,
        'taille' => 1,
        'label' => 'Nom',
        'placeholder' => 'Nom',
        'adds' => [],
    ])
    @endcomponent

    @component('components.input', [
        'type' => 'text',
        'name' => 'prenom',
        'value' => old('prenom') ?? ($prenom ?? ''),
        'optional' => true,
        'taille' => 1,
        'label' => 'Prénom',
        'placeholder' => 'Prénom',
        'adds' => [],
    ])
    @endcomponent

    @component('components.input', [
        'type' => 'text',
        'name' => 'login',
        'value' => old('login') ?? ($login ?? ''),
        'optional' => false,
        'taille' => 1,
        'label' => 'Login',
        'placeholder' => 'Login',
        'adds' => [],
    ])
    @endcomponent

    @component('components.input', [
        'type' => 'password',
        'name' => 'mdp',
        'value' => '',
        'optional' => $method == 1 ? true : false,
        'taille' => 1,
        'label' => 'Mot de passe',
        'placeholder' => 'Passe',
        'adds' => [],
    ])
    @endcomponent

    @component('components.select', [
       'name' => 'formation_id',
       'value' => old('formation_id') ?? ($formation_id ?? ''),
       'optional' => true,
       'taille' => 1,
       'label' => 'Formation',
       'values' => $formations,
       'adds' => [],
       'others' => '',
    ])
    @endcomponent

    @if(auth()->user()->isAdmin())

        @component('components.select', [
           'name' => 'type',
           'value' => old('type') ?? ($type ?? ''),
           'optional' => true,
           'taille' => 1,
           'label' => 'Type de compte',
           'values' => config('user_role'),
           'adds' => [],
           'others' => '',
        ])
        @endcomponent

    @endif

    @component('components.submit-buttons', [
		'name' => 'Enregistrer',
		'back_button' => true,
	])
    @endcomponent

</form>
