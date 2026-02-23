@aware(['mode' => 'view'])
@props([
    // required
    'name'     => '',
    'value'    => '',
    'options'  => [],
    // optional
    'label'    => '',
    'fieldCss' => '',
    'help'     => ''
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    if ($errors->any()) $value = old($cleanName);
@endphp

<x-tk-base::form.ui.field>
    @if($mode == 'view')
        <p {{ $attributes->merge([ 'class' => 'form-control-plaintext fw-bold' ]) }}>{{ is_array($value) ? implode(', ', $value) : $value }} </p>
    @else
        <select name="{{ $name }}" id="fid_{{ $cleanName }}"
            {{ $attributes->merge([ 'class' => 'form-select' . ( $errors->has($name) ? ' is-invalid' : '') ]) }} >
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
