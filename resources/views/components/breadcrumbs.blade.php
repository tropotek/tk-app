{{-- TODO: create a breadcumb object to render --}}
{{-- See: https://github.com/diglactic/laravel-breadcrumbs --}}
<div class="{{ config('app.resources.layout', 'container') }} mt-3">
    <nav class="breadcrumb-component" aria-label="breadcrumb">
        <ol class="breadcrumb p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/">Item 1</a></li>
            <li class="breadcrumb-item active">Item 2</li>
        </ol>
    </nav>
</div>