@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value',
    // optional
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    $value = old($name, $value ?? '');
@endphp

<x-tk-base::form.ui.field>
    <textarea {{ $attributes->merge([
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => $value,
            'rows'     => 5,
            'readonly' => ($mode == 'view') ? 'readonly' : null,
            'class'    => ($mode == 'view') ?
                'form-control-plaintext fw-bold' :
                'form-control' . ( $errors->has($name) ? ' is-invalid' : ''),
        ]) }}
    >{{ $value }}</textarea>
</x-tk-base::form.ui.field>

