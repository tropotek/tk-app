@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value'    => '',
    // optional
    'type'     => 'text',
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    if ($errors->any()) $value = old($name);
@endphp

<x-tk-base::form.ui.field>
    @if($mode == 'view')
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{{ $value }}</p>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="fid_{{ $name }}" value="{{ $value }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }} />
    @endif
</x-tk-base::form.ui.field>
