<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    // optional
])
@php
    $attributes->merge($cell->getHeaderAttrs()->toArray());
@endphp

<th {{ $attributes->merge(['class' => $cell->getOrderByDir()]) }}>
    @if ($cell->isSortable())
        <a class="noblock" href="{{ $cell->getOrderByUrl() }}">{!! $cell->getHeader() !!}</a>
    @else
        {!! $cell->getHeader() !!}
    @endif
</th>
