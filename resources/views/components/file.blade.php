
{{-- Créer un input pour le formulaire --}}

<div class="form-group">

    <label for="{{ $name }}">
        {{ $label }}
        @if($optional)
            <span class="uk-text-muted">(Optionnel)</span>
        @endif
    </label>

    <br>

    @if($value)
        <img src="{{ asset('storage/'.$value) }}" alt="logo" width="100"/>
    @endif

    <input type="file"
           class="@error('{!! $name !!}') is-invalid @enderror"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ $value }}"
           accept="image/*"
            {{ $optional ? '' : 'required' }}

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
