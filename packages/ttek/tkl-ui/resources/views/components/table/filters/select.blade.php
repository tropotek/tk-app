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
        $value = $table->filterVals[$filter->getKey()] ?? $filter->getDefaultValue();
        $attributes = $attributes->merge([
            'wire:model.live' => "filterVals.{$filter->getKey()}",
            'wire:change' => "updateFilters('{$filter->getKey()}', \$event.target.value)",
        ]);
    } else {
        $filterKey = $filter->getTable()->tableKey($filter->getTable()::QUERY_FILTER);
        $filterVals = request()->input($filterKey) ?? [];
        $value = $filterVals[$filter->getKey()] ?? $filter->getDefaultValue();

        $attributes = $attributes->merge([
            'onChange' => 'this.form.submit()',
            'name' => sprintf('%s[%s]', $filterKey, $filter->getKey()),
        ]);
    }
    $attributes = $attributes->merge([
        'class' => 'form-select form-select-sm w-auto',
    ]);
    $attributes = $attributes->merge($filter->getAttrs()->all());
@endphp

<select {{ $attributes }}>
    <option value="">{{ $filter->getLabel() }}</option>
    @foreach($filter->getOptions() as $optVal => $label)
        <option value="{{ $optVal }}" @selected((string)$value === (string)$optVal)>
            {{ $label }}
        </option>
    @endforeach
</select>
