<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    'row',
])

<td {{ $attributes->merge($cell->getAttributes()->toArray()) }}>
    {!! $cell->getHtml($row) !!}
</td>
