<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    'row',
    // optional
])

<td {{ $attributes->merge($cell->getAttributes()->toArray()) }}>
    {!! $cell->getHtml($row) !!}
</td>
