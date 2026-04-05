<?php
/** @var \Tk\Tbl\Cell $cell */
?>
@props([
    // required
    'cell',
    'row'
])

<td {{ $attributes->merge($cell->getAttrs()->toArray()) }}>
    @if (!empty($slot) && $slot->hasActualContent())
        {{ $slot }}
    @else
        {!! $cell->html($row) !!}
    @endif
</td>
