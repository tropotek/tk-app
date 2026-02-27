@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value',
    'viewValue' => '',
    // optional
    'type'     => 'text',
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    $value = old($name, $value ?? '');
@endphp

<x-tk-base::form.ui.field>
    <input {{ $attributes->merge([
            'type'     => $type,
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => $value,
            'readonly' => ($mode == 'view') ? 'readonly' : null,
            'class'    => ($mode == 'view') ?
                'form-control-plaintext fw-bold' :
                'form-control fw-bold' . ( $errors->has($name) ? ' is-invalid' : ''),
        ]) }}
    />
</x-tk-base::form.ui.field>
