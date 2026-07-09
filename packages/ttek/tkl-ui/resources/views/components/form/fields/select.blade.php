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
    $errorKey = $attributes->get('wire:model') ?: $cleanName;
    if (!$withField) {
        $mode = 'edit';
    }
@endphp

<x-tkl-ui::form.ui.field :$errorText :$withField :$errorKey>
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
                'class' => 'form-select' . ( $errors->has($errorKey) ? ' is-invalid' : '')
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
</x-tkl-ui::form.ui.field>
