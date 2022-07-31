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

    @component('components.select', [
       'name' => 'cours_id',
       'value' => old('cours_id') ?? ($cours_id ?? ''),
       'optional' => false,
       'taille' => 1,
       'label' => 'Cours',
       'values' => $cours,
       'adds' => [],
       'others' => '',
    ])
    @endcomponent

    @component('components.input', [
        'type' => 'datetime-local',
        'name' => 'date_debut',
        'value' => old('date_debut') ?? (isset($date_debut) ? $date_debut->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')),
        'optional' => true,
        'taille' => 1,
        'label' => 'Date de début',
        'placeholder' => 'YYYY-MM-DD HH:MM',
        'adds' => [],
    ])
    @endcomponent

    @component('components.input', [
        'type' => 'datetime-local',
        'name' => 'date_fin',
        'value' => old('date_fin') ?? (isset($date_fin) ? $date_fin->format('Y-m-d\TH:i') : date('Y-m-d\TH:i', time() + 3600 * 5)),
        'optional' => true,
        'taille' => 1,
        'label' => 'Date de fin',
        'placeholder' => 'YYYY-MM-DD HH:MM',
        'adds' => [],
    ])
    @endcomponent

    @component('components.submit-buttons', [
		'name' => 'Enregistrer',
		'back_button' => true,
	])
    @endcomponent

</form>
