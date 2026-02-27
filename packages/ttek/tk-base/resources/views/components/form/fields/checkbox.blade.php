@aware(['mode' => 'view'])
@props([
    //required
    'name'     => '',
    'options'  => [],
    'value',
    // optional
    'label'    => '',
    'fieldCss' => '',
    'help'     => '',
    'isSwitch' => false,
    'errorText' => '',
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = old($cleanName, $value ?? '');
@endphp

<x-tk-base::form.ui.field :$errorText>
    @foreach ($options as $optValue => $text)
        <div class="{{$isSwitch ? 'form-switch' : 'form-check'}} {{$errors->has($name) ? ' is-invalid' : ''}}">
            @if($mode == 'view')
                @php
                    $css = 'text-primary ' . (\Tk\Utils\Form::isSelected($optValue, $value) ? ' fa-regular fa-square-check' : 'fa-regular fa-square');
                    if ($isSwitch) {
                        $css = \Tk\Utils\Form::isSelected($optValue, $value) ? 'text-primary fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off';
                    }
                @endphp
                <i class="fa-lg {{ $css }}"></i>
                <span class="fw-bold text-muted">{{ $text }}</span>
            @else
                <input {{ $attributes->merge([
                        'type'     => 'checkbox',
                        'name'     => $name,
                        'id'       => sprintf('fid-%s-%s', $cleanName, $optValue),
                        'value'    => $optValue,
                        'checked'  => \Tk\Utils\Form::isSelected($optValue, $value) ? 'checked' : null,
                        'class'    => 'form-check-input' . ( $errors->has($name) ? ' is-invalid' : ''),
                    ]) }}
                />
                <label class="form-check-label fw-bold" for="fid-{{ $cleanName }}-{{ $optValue }}">{{ $text }}</label>
            @endif
        </div>
    @endforeach
</x-tk-base::form.ui.field>
