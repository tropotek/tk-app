<?php
/** @var \Tk\Table\Cell $cell */
?>
@aware ([
    'table',
])
@props([
    // required
    'row',
    'idx' => 0,
])

<tr {{
    $attributes->merge($table->getRowAttrs($row))
}}>
    @foreach ($table->getCells($row) as $cell)
        <x-tk-base::table.cell :$row :$cell />
    @endforeach
</tr>
