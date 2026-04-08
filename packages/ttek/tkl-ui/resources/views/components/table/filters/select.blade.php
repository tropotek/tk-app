<?php
/** @var \Tk\Table\Table $table */
?>
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
@endphp
<div class="p-2 pe-0">
    <x-tkl-ui::form.fields.select
        :$name
        :$options
        :$value
        :$label
        :withField="false"
        class="form-select-sm w-auto"
        :attributes="$attributes"
    />
</div>
