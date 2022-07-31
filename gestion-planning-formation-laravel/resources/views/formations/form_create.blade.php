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
        'name' => 'intitule',
        'value' => old('intitule') ?? ($intitule ?? ''),
        'optional' => false,
        'taille' => 1,
        'label' => 'Intitulé',
        'placeholder' => 'Intitulé',
        'adds' => [],
    ])
    @endcomponent

    @component('components.submit-buttons', [
		'name' => 'Enregistrer',
		'back_button' => true,
	])
    @endcomponent

</form>
