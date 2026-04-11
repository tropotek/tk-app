@php
    /** @var \Tk\Table\Table $table */
@endphp
@aware(['table'])

@props([
    // required
    'name'      => '',
    'options'   => [],
    'value',
    // optional
    'label'     => '',
])
@php
    if (!$table->isLivewire()) {
        $attributes = $attributes->merge(['onChange' => 'this.form.submit()']);
    }
    $attributes = $attributes->merge(['class' => 'form-select-sm w-auto']);
@endphp
<div class="p-2 pe-0">
    <x-tkl-ui::form.fields.select
        :$name
        :$options
        :$value
        :$label
        :withField="false"
        :attributes="$attributes"
    />
</div>
