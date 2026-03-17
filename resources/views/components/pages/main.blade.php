@props(['pageName'])
<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageName }} {{ config('app.name', 'Example App') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

</head>
<body class="d-flex flex-column h-100 bg-white">

    <x-pages.meta.header />

    <main class="flex-grow-1 pt-5 mt-3">

        <div class="{{ config('app.resources.layout', 'container') }}">

            <x-tkl-ui::breadcrumbs />
            <x-ui.alerts />

            <div class="mb-5 clearfix">
                {{ $slot }}
            </div>
        </div>

    </main>

    <x-pages.meta.footer />

    <x-ui.modal.about />
</body>
</html>
