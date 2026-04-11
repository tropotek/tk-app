@php
    /** @var \Tk\Table\Filter $filter */
@endphp
@aware(['table'])
@props([
    // required
    'filter',
])
@php
    if ($table->isLivewire()) {
        $value = $filter->getTable()->filterVals[$filter->getKey()] ?? $filter->getDefaultValue();
        $attributes = $attributes->merge([
            'wire:model.live' => "filterVals.{$filter->getKey()}",
            'wire:change' => "updateFilters('{$filter->getKey()}', \$event.target.value)",
        ]);
    } else {
        $key = $filter->getTable()->tableKey($filter->getKey());
        $value = request()->input($key, '') ??$filter->getDefaultValue();
        $attributes = $attributes->merge([
            'name' => $key,
            // todo mm: use on change/onclick to trigger auto submit
            'onChange' => 'this.form.submit()',
        ]);
    }
    $attributes = $attributes->merge($filter->getAttrs()->all());
@endphp

<div class="form-check form-switch ms-1">
    <input {{ $attributes->merge([
            'type' => 'checkbox',
            'class' => 'form-check-input',
            'name' => $filter->getTable()->tableKey($filter->getKey()),
            'placeholder' => $filter->getLabel(),
            'checked' => in_array($value, [1, '1', true, 'true', 'on'], true) ? true : null,,
        ]) }}
    />
    <label class="form-check-label small">{{ $filter->getLabel() }}</label>
</div>

