@aware([
    'mode' => 'view',
    'method' => '/',
])
@props([
    // required
    'mode'    => 'view',
    'method'  => 'post',
    // optional
    'buttons',
    'fields',
])
@php
    // set enctype for file uploads if not already set, and a file field exists
    if(
        !empty($fields) && $fields->hasActualContent() &&
        empty($enctype) && str_contains($fields->toHtml(), '<input type="file"')
    ) {
        $attributes = $attributes->merge(['enctype' => 'multipart/form-data']);
    }
@endphp

<form
    {{ $attributes->merge([
        // Added the class `mode-{$mode}` to enable mode-based styling
        'class'     => 'tk-form needs-validation g-3 mode-' . $mode,
        'method'    => (in_array(strtolower($method), ['get', 'post'])) ? $method : 'post',
        'id'        => 'theform',
        'novalidate' => '',     // disabled for error testing
    ]) }}
>

    @csrf

    {{-- Enable non standard requst methods --}}
    @if(!empty($method) && !in_array(strtolower($method), ['get', 'post'])) @method($method) @endif

    @if(!empty($buttons) && $buttons->hasActualContent())
        <div class="tk-actions d-grid gap-2 d-md-flex mb-3">
            {{ $buttons }}
        </div>
    @endif

    @if(!empty($fields) && $fields->hasActualContent())
        <div class="tk-form-fields row g-3">
            {{ $fields }}
        </div>
    @endif

</form>
