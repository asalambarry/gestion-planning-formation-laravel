
    {{-- Boutons de modifications et suppréssion --}}

    @if($back_button)
        <button class="btn btn-dark btn-sm" type="button"
                onclick="window.history.back();" title="Rétourner">
            <i class="fas fa-history"></i>
        </button> &nbsp;
    @endif

    {{-- Mettre à jour --}}
    @if($buttons)
        <a class="btn btn-primary btn-small" href="{!! route($edit_route, [$id]) !!}" title="Modifier">
            Modifier
        </a>
    @else
        <a href="{!! route($edit_route, [$id]) !!}" class="btn btn-link" title="Modifier">
         Modifier</a>
    @endif

    {{-- Supprimer --}}

    @if($buttons)
        <button class="btn btn-danger btn-small" type="button"
            title="Supprimer"
                onclick="if(confirm('Souhaitez-vous vraiment supprimer cet élement ?'))
                        document.querySelector('#destroy-{!!  $name !!}-{!! $id !!}').submit();">
            Supprimer
        </button>
    @else
        <a href="javascript:void(0)" class="btn btn-link text-danger"
            title="Supprimer"
           onclick="if(confirm('Souhaitez-vous vraiment supprimer cette {{ $name }} ?'))
                   document.querySelector('#destroy-{{ $name }}-{{ $id }}').submit();">
            Supprimer</a>
    @endif

    <form id="destroy-{!! $name !!}-{!! $id !!}" action="{!! route($destroy_route, [$id]) !!}" method="POST">
        @csrf
        @method('DELETE')
    </form>

    {{-- @endif --}}
