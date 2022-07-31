
{{-- Créer un input pour le formulaire --}}

<div class="form-group">

    <label class="" for="{{ $name }}">
        {{ $label }}
        @if($optional)
            <span class="text-muted">(Optionnel)</span>
        @endif
    </label>

    <textarea cols="20" rows="10"
          class="form-control @error('{{ $name }}') is-invalid @enderror"
          id="{{ $name }}"
          name="{{ $name }}"
          autocomplete="{{ $name }}"
          placeholder="{{ $placeholder }}"
          minlength="3"
          {{ $optional ? '' : 'required' }}
          @foreach($adds as $elt)
            {!! $elt !!}
         @endforeach
    >{!! $value !!}</textarea>

    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror

</div>
