@aware(['mode' => 'view'])
@props([
    // required
    'name'      => '',
    'options'   => [],
    'value',
    // optional
    'label'     => '',
    'fieldCss'  => '',
    'help'      => '',
    'errorText' => '',
    'withField' => true,
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = old($cleanName, $value ?? '');
    if (!$withField) {
        $mode = 'edit';
    }
@endphp

<x-tk-base::form.ui.field :$errorText :$withField>
    @if($mode == 'view')
        <input {{ $attributes->merge([
            'type'     => 'text',
            'name'     => $name,
            'id'       => 'fid-'.$cleanName,
            'value'    => is_array($value) ? implode(', ', $value) : $value,
            'readonly' => 'readonly',
            'class'    => 'form-control-plaintext',
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
