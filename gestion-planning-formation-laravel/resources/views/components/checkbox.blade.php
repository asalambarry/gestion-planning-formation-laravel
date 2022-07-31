
{{-- Créer un radio pour le formulaire --}}

<div class="form-group">

        <label for="{{ $name }}">
            {{ $label }}
            @if($optional)
                <span class="uk-text-muted">(Optionnel)</span>
            @endif
        </label>

        <div class="form-check">

            {!! $others ?? '' !!}

            @foreach($values as $key => $val)

                <label class="form-check-label">

                    <input id="{{ $name }}" name="{{ $name }}"
                            type="radio" class="form-check-input"
                            value="{{ $key }}" {{ $value == $key ? 'checked' : '' }}
                             {{ $optional ? '' : 'required' }}
                            @foreach($adds as $elt)
                                {!! $elt !!}
                            @endforeach
                     >

                    {{ $val }}</label> &nbsp; &nbsp;

            @endforeach

            @error($name)
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
</div>
