<?php
/** @var \Tk\Tbl\Table $table */
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
    <x-tkl-ui::form.fields.select
        :$name
        :$options
        :$value
        :$label
        :withField="false"
        class="form-select-sm w-auto"
        onChange="this.form.submit()"
        :attributes="$attributes"
    />
</div>
