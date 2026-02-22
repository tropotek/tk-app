@aware(['mode' => 'view', 'values' => []])
@props([
    // required
    'name' => '',
    // optional
    'type' => 'text',
    'label' => '',
    'default' => '',
    'fieldCss' => '',
    'help' => ''
])
@php
    $value = $values[$name] ?? $default;
    if ($errors->any()) $value = old($name);
@endphp

<x-tk-base::form.ui.field>
    @if($mode == 'edit')
        <input type="{{ $type }}" name="{{ $name }}" id="fid_{{ $name }}" value="{{ $value }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }} />
    @else
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{{ $value }}</p>
    @endif
</x-tk-base::form.ui.field>
