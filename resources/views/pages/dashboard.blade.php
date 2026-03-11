<x-pages.main>
    <div class="container">
        <div class="col text-center">
            @auth
                <h1 class="text-primary">Welcome {{ auth()->user()->name }}</h1>
            @else
                <h1 class="text-primary">Hello, world.</h1>
            @endauth
        </div>

        <p>Test flash message alerts:</p>
        <ul class="list-unstyled">&nbsp;
            <a class="btn btn-success" href="{{ request()->fullUrlWithQuery(['alert' => 'success']) }}">Success</a>
            <a class="btn btn-info" href="{{ request()->fullUrlWithQuery(['alert' => 'info']) }}">Info</a>
            <a class="btn btn-warning" href="{{ request()->fullUrlWithQuery(['alert' => 'warning']) }}">Warning</a>
            <a class="btn btn-danger" href="{{ request()->fullUrlWithQuery(['alert' => 'danger']) }}">Danger</a>
            <a class="btn btn-danger" href="{{ request()->fullUrlWithQuery(['alert' => 'error']) }}">Error</a>
        </ul>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

        <div>
        </div>
    </div>
</x-pages.main>
