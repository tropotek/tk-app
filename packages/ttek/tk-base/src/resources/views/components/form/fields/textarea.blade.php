@aware(['mode' => 'view', 'values' => []])
@props([
    // required
    'name' => '',
    // optional
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
        <textarea name="{{ $name }}" id="fid_{{ $name }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : ''), 'rows' => '5' ]) }}
        >{{ $value }}</textarea>
    @else
        {{-- TODO: Might need a flag to determin if the value is HTML or not. --}}
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{!! $value !!}</p>
    @endif
</x-tk-base::form.ui.field>

