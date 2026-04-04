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

<x-tkl-ui::form.ui.field :$errorText>
    <input {{ $attributes->merge([
            'type'     => $type,
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => $value,
            'readonly' => ($mode == 'view') ? 'readonly' : null,
            'class'    => ($mode == 'view') ?
                'form-control-plaintext' :
                'form-control' . ( $errors->isNotEmpty() ? ' is-invalid' : ''),
        ]) }}
    />
</x-tkl-ui::form.ui.field>
