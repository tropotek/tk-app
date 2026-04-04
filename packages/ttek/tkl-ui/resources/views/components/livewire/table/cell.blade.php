<?php
/** @var \Tk\Livewire\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    'row',
])

<td {{ $attributes->merge($cell->getAttrs()->toArray()) }}>
    {!! $cell->getHtml($row) !!}
</td>
