@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value'    => '',
    // optional
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    if ($errors->any()) $value = old($name);
@endphp

<x-tk-base::form.ui.field>
    @if($mode == 'view')
        {{-- TODO: Might need a flag to determin if the value is HTML or not. --}}
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{!! $value !!}</p>
    @else
        <textarea name="{{ $name }}" id="fid_{{ $name }}"
            {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : ''), 'rows' => '5' ]) }}
        >{{ $value }}</textarea>
    @endif
</x-tk-base::form.ui.field>

