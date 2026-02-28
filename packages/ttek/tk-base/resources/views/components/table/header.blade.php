<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    // optional
])

<th {{ $attributes->merge($cell->getHeaderAttrs()->toArray()) }}>
    @if ($cell->isSortable())
        <a class="noblock" href="{{ $cell->getOrderByUrl() }}">{!! $cell->getHeader() !!}</a>
    @else
        {!! $cell->getHeader() !!}
    @endif
</th>
