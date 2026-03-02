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
        @if($cell->getComponent() && $cell->componentExists($cell->getComponent()))
            <x-dynamic-component :component="$cell->getComponent()" :$row :$cell/>
        @else
            <x-tk-base::table.cell :$row :$cell/>
        @endif
    @endforeach
</tr>
