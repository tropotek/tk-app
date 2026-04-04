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

<x-tkl-ui::form.ui.field :$errorText>
    @foreach ($options as $optValue => $text)
        <div class="form-check {{ $errors->isNotEmpty() ? ' is-invalid' : '' }}">
            @if($mode == 'view')
                <i class="text-primary fa-lg {{ \Tk\Utils\Form::isSelected($optValue, $value) ? ' fa-regular fa-circle-dot' : 'fa-regular fa-circle' }}"></i>
                <span class="text-muted">{{ $text }}</span>
            @else
                <input {{ $attributes->merge([
                        'type'     => 'radio',
                        'name'     => $name,
                        'id'       => sprintf('fid-%s-%s', $name, $optValue),
                        'value'    => $optValue,
                        'checked'  => \Tk\Utils\Form::isSelected($optValue, $value) ? 'checked' : null,
                        'class'    => 'form-check-input' . ( $errors->isNotEmpty()? ' is-invalid' : ''),
                    ]) }}
                />
                <label class="form-check-label" for="fid-{{ $name }}-{{ $optValue }}">{{ $text }}</label>
            @endif
        </div>
    @endforeach
</x-tkl-ui::form.ui.field>
