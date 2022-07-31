
 @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <p>
            {{ session('info') }}
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
