{{-- Field Grouper: Group fields within a fieldset --}}
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
