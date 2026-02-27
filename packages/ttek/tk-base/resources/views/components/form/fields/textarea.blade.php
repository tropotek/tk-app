@aware(['mode' => 'view'])
@props([
    // required
    'name'      => '',
    'value',
    // optional
    'label'     => '',
    'fieldCss'  => '',
    'help'      => '',
    'errorText' => '',
])
@php
    $value = old($name, $value ?? '');
@endphp

<x-tk-base::form.ui.field :$errorText>
    @if ($mode == 'view')
        <div class="form-control-plaintext fw-bold">{{ $value }}</div>
    @else
        <textarea {{ $attributes->merge([
                'name'     => $name,
                'id'       => 'fid-'.$name,
                'value'    => $value,
                'rows'     => 5,
                'class'    => 'form-control fw-bold' . ( $errors->has($name) ? ' is-invalid' : ''),
            ]) }}
        >{{ $value }}</textarea>
    @endif
</x-tk-base::form.ui.field>

