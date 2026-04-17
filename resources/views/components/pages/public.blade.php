@props(['pageTitle' => ''])
<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageName }} {{ config('app.name', 'Example App') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @livewireStyles

</head>
<body class="bg-light h-100">

    <div class="content-box d-flex justify-content-center align-items-center vh-100">
        <div class="p-4 bg-white rounded col-3">
            {{ $slot }}

            <p class="mb-0 mt-auto py-1 text-center">
                <a href="/" class="small text-muted text-decoration-none">
                    <span>{{ config('app.name', 'Example App') }}</span> &copy; <span>{{ date('Y') }}</span>
                </a>
            </p>
        </div>
    </div>

    @livewireScriptConfig
</body>
</html>
