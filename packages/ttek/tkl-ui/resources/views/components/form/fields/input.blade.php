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
    $errorKey = $attributes->get('wire:model') ?: $name;
@endphp

<x-tkl-ui::form.ui.field :$errorText :$errorKey>
    <input {{ $attributes->merge([
            'type'     => $type,
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => $value,
            'readonly' => ($mode == 'view') ? 'readonly' : null,
            'class'    => ($mode == 'view') ?
                'form-control-plaintext' :
                'form-control' . ( $errors->has($errorKey) ? ' is-invalid' : ''),
        ]) }}
    />
</x-tkl-ui::form.ui.field>
