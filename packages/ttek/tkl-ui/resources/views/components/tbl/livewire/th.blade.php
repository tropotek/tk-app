<?php
/** @var \Tk\Tbl\Cell $cell */
?>
@props([
    // required
    'cell'
])

<th {{ $attributes->merge(['class' => $cell->isSortable() ? 'col-sort'  : '']) }}>
    @if ($cell->isSortable())
        <button
            class="btn btn-link fw-bold text-decoration-none {{ ($cell->getTable()->sort === $cell->getName()) ? $cell->getTable()->dir : ''  }}"
            wire:click="{{ ($cell->getTable()->sort === $cell->getName()) ? 'toggleDir' : '$set("sort", "' . $cell->getName() . '")' }}">
            {{ $cell->getHeader() }}
        </button>
    @else
        <span class="fw-bold">{{ $cell->getHeader() }}</span>
    @endif
</th>
