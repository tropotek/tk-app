@aware(['mode' => 'view'])
@props([
    // required
    'name'    => '',
    'value',
])
@php
    $value = old($name, $value ?? '');
@endphp

<input {{ $attributes->merge([
        'type'     => 'hidden',
        'name'     => $name,
        'id'       => 'fid-'.$name,
        'value'    => $value,
        'readonly' => ($mode == 'view') ? 'readonly' : null,
    ]) }}
/>
