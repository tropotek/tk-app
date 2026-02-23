@aware(['mode' => 'view'])
@props([
    // required
    'name'    => '',
    'value'   => '',
])
@php
    if ($errors->any()) $value = old($name);
@endphp

@if($mode != 'view')
    <input type="hidden" name="{{ $name }}" id="fid_{{ $name }}" value="{{ $value }}"
           {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }}/>
@endif
