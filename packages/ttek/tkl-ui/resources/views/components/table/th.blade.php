@php
    /** @var \Tk\Table\Table $table */
@endphp
@props([
    // required
    'cell',
])

@if($cell->isVisible())

    @php
        $wireClick = null;
        if ($cell->isSortable() && $cell->getTable()->isLivewire()) {
            $wireClick = $cell->getTable()->sort === $cell->getSort()
                ? 'toggleDir'
                : "setSort('" . $cell->getSort() . "')";
        }
    @endphp

    <th {{ $attributes->class([
            'fw-bold text-decoration-none' => true,
            'col-sort text-primary text-decoration-underline clickable' => $cell->isSortable(),
            $cell->getTable()->dir => $cell->getTable()->sort === $cell->getSort(),
        ]) }}
        @if ($wireClick)
            wire:click="{{ $wireClick }}"
        @endif
    >
        <span>{!! $cell->getHeader() !!}</span>
        @if($cell->getTable()->sort === $cell->getSort())
            <i class="fa {{ $cell->getTable()->dir === 'asc' ? 'fa-sort-down' : 'fa-sort-up' }}"></i>
        @endif
    </th>

@endif
