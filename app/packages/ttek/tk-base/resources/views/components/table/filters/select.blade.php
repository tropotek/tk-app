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

<div class="p-2 pe-0">
    <x-tk-base::form.fields.select
        :$name
        :$options
        :$value
        :$label
        :withField="false"
        class="form-select-sm"
        onChange="this.form.submit()"
        :attributes="$attributes"
    />
</div>
