@aware([
    'mode' => 'view',
    'values' => [],
])
@props([
    // required
    'mode' => 'view',
    'values' => [],
    // optional
    'buttons' => '',
    'fields'  => '',
    'enctype' => '',
])
@php
    // set enctype for file uploads if not already set, and a file field exists
    if(str_contains($fields->toHtml(), '<input type="file"') && empty($enctype)) {
        $enctype = 'multipart/form-data';
    }
@endphp

<form
    {{ $attributes->merge([
        // Added the class `mode-{$mode}` to enable mode-based styling
        'class'     => 'tk-form needs-validation g-3 mode-' . $mode,
        'method'    => 'post',
        'id'        => 'theform',
        'novalidate' => '',
    ]) }}
    @if(!$attributes->has('enctype') && !empty($enctype)) enctype="{{ $enctype }}" @endif
>

    @csrf

{{-- kept for the REST pattern controllers, when implemented --}}
{{--    @if ($mode == 'create') @method('PUT') @endif--}}
{{--    @if ($mode == 'edit') @method('PATCH') @endif--}}

    <div class="tk-actions d-grid gap-2 d-md-flex mb-3">
        {{ $buttons }}
    </div>

    <div class="tk-form-fields row g-3">
        {{ $fields }}
    </div>

</form>
