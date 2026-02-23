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
    if ($errors->any()) $value = old($name);
@endphp

<x-tk-base::form.ui.field>
    @foreach ($options as $optValue => $text)
        <div class="form-check {{$errors->has($name) ? ' is-invalid' : ''}}">
            @if($mode == 'view')
                <i class="text-primary fa-lg {{ \Tk\Utils\Form::isSelected($optValue, $value) ? ' fa-regular fa-circle-dot' : 'fa-regular fa-circle' }}"></i>
                <span class="fw-bold text-muted">{{ $text }}</span>
            @else
                <input type="radio" class="form-check-input"
                       name="{{ $name }}" id="fid_{{ $name }}_{{ $optValue }}" value="{{ $optValue }}"
                    {{ $attributes->merge([ 'class' => 'form-control' . ( $errors->has($name) ? ' is-invalid' : '') ]) }}
                    {{ \Tk\Utils\Form::isSelected($optValue, $value) ? 'checked' : '' }} />
                <label class="form-check-label fw-bold" for="fid_{{ $name }}_{{ $optValue }}">{{ $text }}</label>
            @endif
        </div>
    @endforeach
</x-tk-base::form.ui.field>
