@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value',
    'options'  => [],
    // optional
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = old($cleanName, $value ?? '');
@endphp

<x-tk-base::form.ui.field>
    @if($mode == 'view')
        <input {{ $attributes->merge([
            'type'     => 'text',
            'name'     => $name,
            'id'       => 'fid-'.$name,
            'value'    => is_array($value) ? implode(', ', $value) : $value,
            'readonly' => 'readonly',
            'class'    => 'form-control-plaintext fw-bold',
        ]) }}
        />
    @else
        <select {{ $attributes->merge([
                'name'  => $name,
                'id'    => 'fid-'.$cleanName,
                'class' => 'form-select' . ( $errors->has($name) ? ' is-invalid' : '')
            ]) }}
        >
            @foreach ($options as $val => $text)

                @if (is_array($text))
                    <optgroup label="{{ $val }}">
                        @foreach ($text as $v => $t)
                            <option value="{{ $v }}" @selected(\Tk\Utils\Form::isSelected($val, $value))>{{ $t }}</option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $val }}" @selected(\Tk\Utils\Form::isSelected($val, $value))>{{ $text }}</option>
                @endif

            @endforeach
        </select>
    @endif
</x-tk-base::form.ui.field>
