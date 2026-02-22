<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Example App') }}</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
{{--    <script src="{{ asset('js/htmx.min.js') }}"></script>--}}
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body class="d-flex flex-column h-100 bg-white">

    <x-header />

    <main class="flex-grow-1 pt-5">
        <x-breadcrumbs />
        <x-alerts />

        <div class="{{ config('app.resources.layout', 'container') }}">
            <div class="mb-5 clearfix">
                {{ $slot }}
            </div>
        </div>

    </main>

    <x-footer />

</body>
</html>
