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
        @if($cell->getType())
            <x-dynamic-component :component="'tk-base::table.cell.' . $cell->getType()" :$row :$cell />
        @else
            <x-tk-base::table.cell :$row :$cell />
       @endif
    @endforeach
</tr>
