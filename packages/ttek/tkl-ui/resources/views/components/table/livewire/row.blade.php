<?php
/** @var \Tk\Livewire\Table\Cell $cell */
?>
@props([
    // required
    'table',
    'row',
    'idx' => 0,
])

<tr {{
    $attributes->merge($table->getRowAttrs($row))
}}>
    @foreach ($table->getCells($row) as $cell)
        @if($cell->getComponent() && $cell->componentExists($cell->getComponent()))
            <x-dynamic-component :component="$cell->getComponent()" :$row :$cell />
        @else
            <x-tkl-ui::table.livewire.cell :$row :$cell />
        @endif
    @endforeach
</tr>
