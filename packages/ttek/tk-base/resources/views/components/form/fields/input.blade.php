@aware(['mode' => 'view'])
@props([
    // required
    'name'      => '',
    'viewValue' => '',
    'value',
    // optional
    'type'      => 'text',
    'label'     => '',
    'fieldCss'  => '',
    'help'      => '',
    'errorText' => '',
])
@php
    $value = old($name, $value ?? '');
@endphp

<x-tk-base::form.ui.field :$errorText>
    <input {{ $attributes->merge([
            'type'     => $type,
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => $value,
            'readonly' => ($mode == 'view') ? 'readonly' : null,
            'class'    => ($mode == 'view') ?
                'form-control-plaintext' :
                'form-control' . ( $errors->has($name) ? ' is-invalid' : ''),
        ]) }}
    />
</x-tk-base::form.ui.field>
