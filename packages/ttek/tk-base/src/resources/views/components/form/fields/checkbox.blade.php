@aware(['mode' => 'view', 'values' => []])
@props([
    //required
    'name' => '',
    'options' => [],
    // optional
    'label' => '',
    'default' => [],
    'fieldCss' => '',
    'help' => '',
    'isSwitch' => false,
])
@php
    $cleanName = str_replace(['[', ']'], '', $name);
    $value = $values[$name] ?? $default;
    if ($errors->any()) $value = old($cleanName);
@endphp

<x-tk-base::form.ui.field>
    @foreach ($options as $optValue => $text)
        <div class="{{$isSwitch ? 'form-switch' : 'form-check'}} {{$errors->has($name) ? ' is-invalid' : ''}}">
            @if($mode == 'edit')
                <input type="checkbox" class="form-check-input"
                        name="{{ $name }}" id="fid_{{ $cleanName }}_{{ $optValue }}" value="{{ $optValue }}"
                        {{ $attributes->merge([ 'class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '') ]) }}
                        {{ \Tk\Utils\Form::isSelected($optValue, $value) ? 'checked' : '' }} />
                <label class="form-check-label fw-bold" for="fid_{{ $cleanName }}_{{ $optValue }}">{{ $text }}</label>
            @else
                @php
                    $css = 'text-primary ' . (\Tk\Utils\Form::isSelected($optValue, $value) ? ' fa-regular fa-square-check' : 'fa-regular fa-square');
                    if ($isSwitch) {
                        $css = \Tk\Utils\Form::isSelected($optValue, $value) ? 'text-primary fa-solid fa-toggle-on' : 'fa-solid fa-toggle-off';
                    }
                @endphp
                <i class="fa-lg {{ $css }}"></i>
                <span class="fw-bold text-muted">{{ $text }}</span>
            @endif
        </div>
    @endforeach
</x-tk-base::form.ui.field>
