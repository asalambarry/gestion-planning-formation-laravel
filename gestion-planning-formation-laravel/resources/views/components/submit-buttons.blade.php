
{{-- Boutons de modification & suppression --}}
<div class="col-md text-right">

    <hr>

	@if($back_button)
		<a href="#" class="btn btn-secondary" type="button"
            onclick="window.history.back();">Quitter</a>
	@endif

    <button class="btn btn-primary" type="submit" name="action">
        {{ $name }}
    </button>

</div>
