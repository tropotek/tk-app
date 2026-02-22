@aware(['mode' => 'view', 'values' => []])
@props([
    // required
    'name' => '',
    // optional
    'default' => '',
])
@php
    $value = $values[$name] ?? $default;
    if ($errors->any()) $value = old($name);
@endphp

@if($mode == 'edit')
    <input type="hidden" name="{{ $name }}" id="fid_{{ $name }}" value="{{ $value }}"
           {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }}/>
@endif
