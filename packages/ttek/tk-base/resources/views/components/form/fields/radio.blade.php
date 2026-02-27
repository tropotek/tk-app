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
])
@php
    $value = old($name, $value ?? '');
@endphp

<x-tk-base::form.ui.field :$errorText>
    @foreach ($options as $optValue => $text)
        <div class="form-check {{$errors->has($name) ? ' is-invalid' : ''}}">
            @if($mode == 'view')
                <i class="text-primary fa-lg {{ \Tk\Utils\Form::isSelected($optValue, $value) ? ' fa-regular fa-circle-dot' : 'fa-regular fa-circle' }}"></i>
                <span class="fw-bold text-muted">{{ $text }}</span>
            @else
                <input {{ $attributes->merge([
                        'type'     => 'radio',
                        'name'     => $name,
                        'id'       => sprintf('fid-%s-%s', $name, $optValue),
                        'value'    => $optValue,
                        'checked'  => \Tk\Utils\Form::isSelected($optValue, $value) ? 'checked' : null,
                        'class'    => 'form-check-input' . ( $errors->has($name) ? ' is-invalid' : ''),
                    ]) }}
                />
                <label class="form-check-label fw-bold" for="fid-{{ $name }}-{{ $optValue }}">{{ $text }}</label>
            @endif
        </div>
    @endforeach
</x-tk-base::form.ui.field>
