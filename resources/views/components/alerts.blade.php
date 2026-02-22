{{-- TODO: render alerts from the session --}}
<div class="{{ config('app.resources.layout', 'container') }}">
    @foreach (['success', 'error', 'warning', 'info', 'danger'] as $msg)
        @if(session()->has($msg))
            <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
</div>
