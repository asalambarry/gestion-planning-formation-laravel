
{{-- Créer un input pour le formulaire --}}

<div class="form-group">

    <label for="{{ $name }}">

        {{ $label }}

        @if($optional)
            <span class="text-muted">(Optionnel)</span>
        @endif

    </label>

        {!! $others ?? '' !!}

    <input type="{{ $type }}"
           class="form-control @error('{!! $name !!}') is-invalid @enderror"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           placeholder="{{ $placeholder }}"
           minlength="3"
           maxlength="60"
           {!! $optional ? '' : 'required' !!}

        @foreach($adds as $elt)
             {!! $elt !!}
        @endforeach
    />

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror

</div>
