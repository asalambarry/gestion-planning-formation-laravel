
{{-- Créer un select pour le formulaire --}}

<div class="form-group">

    <label for="{{ $name }}">

        {{ $label }}

        @if($optional)
            <span class="text-muted">(Optionnel)</span>
        @endif
    </label>

    <div>
        {!! $others ?? '' !!}
        <select
           class="form-control @error('{!! $name !!}') is-invalid @enderror"
           id="{{ $name }}"
           name="{{ $name }}"
            {{ $optional ? '' : 'required' }}

            @foreach($adds as $elt)
                {!! $elt !!}
            @endforeach
        >
            {{-- Show all values --}}
            <option></option>
            @foreach($values as $key => $val)
                <option {{ $value == $key ? 'selected' : '' }} value="{!! $key !!}">{!! $val !!}</option>
            @endforeach

        </select>

        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

</div>
