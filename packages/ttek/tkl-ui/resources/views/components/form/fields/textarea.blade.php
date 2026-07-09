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
    $errorKey = $attributes->get('wire:model') ?: $name;
@endphp

<x-tkl-ui::form.ui.field :$errorText :$errorKey>
    @if ($mode == 'view')
        <div class="form-control-plaintext">{{ $value }}</div>
    @else
        <textarea {{ $attributes->merge([
                'name'     => $name,
                'id'       => 'fid-'.$name,
                'value'    => $value,
                'rows'     => 5,
                'class'    => 'form-control' . ( $errors->has($errorKey) ? ' is-invalid' : ''),
            ]) }}
        >{{ $value }}</textarea>
    @endif
</x-tkl-ui::form.ui.field>

