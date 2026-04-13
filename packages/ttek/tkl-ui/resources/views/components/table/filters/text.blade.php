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
            'wire:model.live' => "filterVals.{$filter->key}",
            'wire:change' => "updateFilters('$filter->key', \$event.target.value)",
            'value' => $table->filterVals[$filter->getKey()] ?? $filter->getDefaultValue(),
        ]);
    } else {
        $filterKey = $filter->getTable()->tableKey($filter->getTable()::QUERY_FILTER);
        $filterVals = request()->input($filterKey) ?? [];
        $value = $filterVals[$filter->getKey()] ?? '';

        $attributes = $attributes->merge([
            'name' => sprintf('%s[%s]', $filterKey, $filter->getKey()),
            'value' => $value,
            // todo mm: use on blur or something similar to trigger auto submit (alternatively add a submit button)
            //['onChange' => 'this.form.submit()'],
        ]);
    }
    $attributes = $attributes->merge($filter->getAttrs()->all());
@endphp

<input {{ $attributes->merge([
        'type' => 'text',
        'class' => 'form-control form-control-sm w-auto',
        'placeholder' => $filter->getLabel(),
    ]) }}
/>
