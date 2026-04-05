<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
])

<th {{ $attributes->merge(['class' => $cell->isSortable() ? 'col-sort'  : '']) }}>
    @if ($cell->isSortable())
        <a href="{{ $cell->getNextSortUrl($cell->getTable()->sort, $cell->getTable()->dir) }}"
           class="fw-bold {{ ($cell->getTable()->sort === $cell->getName()) ? $cell->getTable()->dir : '' }}">
            {{ $cell->getHeader() }}
        </a>
    @else
        <span class="fw-bold">{{ $cell->getHeader() }}</span>
    @endif
</th>
