<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
])
@if($cell->isVisible())

    @if($cell->getTable()->isLivewire())
        @php
            $wireClick = null;
            if ($cell->isSortable()) {
                $wireClick = $cell->getTable()->sort === $cell->getSort()
                    ? 'toggleDir'
                    : '$set("sort", "' . $cell->getSort() . '")';
            }
        @endphp

        <th {{ $attributes->class([
                'fw-bold text-decoration-none' => true,
                'col-sort text-primary' => $cell->isSortable(),
                $cell->getTable()->dir => $cell->getTable()->sort === $cell->getSort(),
            ]) }}
            @if ($wireClick)
                wire:click="{{ $wireClick }}"
            @endif
        >
            <span>{{ $cell->getHeader() }}</span>
        </th>
    @else
        <th {{ $attributes->class([
                'fw-bold text-decoration-none' => true,
                'col-sort text-primary' => $cell->isSortable(),
                $cell->getTable()->dir => $cell->getTable()->sort === $cell->getSort(),
            ]) }}
        >
            @if ($cell->isSortable())
                <a href="{{ $cell->getNextSortUrl($cell->getTable()->sort, $cell->getTable()->dir) }}">
                    {{ $cell->getHeader() }}
                </a>
            @else
                <span>{{ $cell->getHeader() }}</span>
            @endif
        </th>
    @endif

@endif
