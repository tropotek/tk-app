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
        $attributes = $attributes->merge([
            'wire:model.live' => "filterVals.{$filter->getKey()}",
            'wire:change' => "updateFilters('{$filter->getKey()}', \$event.target.value)",
            'value' => $table->filterVals[$filter->getKey()] ?? $filter->getDefaultValue(),
        ]);
    } else {
        $key = $filter->getTable()->tableKey($filter->getKey());
        $attributes = $attributes->merge([
            'name' => $key,
            'value' => request()->input($key, $filter->getDefaultValue()),
            // todo mm: use on blur or something similar to trigger auto submit (alternatively add a submit button)
            //'onChange' => 'this.form.submit()',
        ]);
    }
    $attributes = $attributes->merge($filter->getAttrs()->all());
@endphp

<input {{ $attributes->merge([
        'type' => 'date',
        'class' => 'form-control form-control-sm w-auto',
        'placeholder' => $filter->getLabel(),
    ]) }}
/>
