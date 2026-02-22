@props([
    // required
    'legend' => ''
])
<div {{ $attributes }}>
    <fieldset>
        <legend>{{ $legend }}</legend>
        <div class="row">
            {{ $slot }}
        </div>
    </fieldset>
</div>
