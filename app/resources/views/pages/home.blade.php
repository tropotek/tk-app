<x-pages.main>
    <div class="container">
        <div class="col text-center">
            <h1 class="text-primary">Example App Home Page</h1>
        </div>

        <p>&nbsp;</p>
        <p><strong><a href="{{ route('login') }}">Login</a> as U: admin@example.com P: password</strong></p>
        <p>&nbsp;</p>

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
