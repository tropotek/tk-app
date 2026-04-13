@php
    /** @var \Tk\Table\Cell $cell */
@endphp
@props([
    // required
    'cell',
])
@if($cell->isVisible())

    @php
        $wireClick = null;
        if ($cell->isSortable() && $cell->getTable()->isLivewire()) {
            $wireClick = $cell->getTable()->getSort() === $cell->getSort()
                ? 'toggleDir'
                : "setSort('" . $cell->getSort() . "')";
        }
        $attributes = $attributes->merge($cell->getHeaderAttrs()->all());
    @endphp

    {{-- todo mm: add an anchor tag here for non-livewire tables. --}}
    <th {{ $attributes->class([
            'fw-bold text-decoration-none' => true,
            'col-sort text-primary text-decoration-none clickable' => $cell->isSortable(),
            $cell->getTable()->getDir() => $cell->getTable()->getSort() === $cell->getSort(),
        ]) }}
        @if ($wireClick)
            wire:click="{{ $wireClick }}"
        @endif
    >
        @if($cell->isSortable() && !$cell->getTable()->isLivewire())
            <a class="sort-btn" href="{{ $cell->getTable()->isLivewire() ? 'javascript:' : $cell->getNextSortUrl() }}">{!! $cell->getHeader() !!}</a>
        @else
            <span
                @if($cell->isSortable() && $cell->getTable()->isLivewire()) class="sort-btn" @endif
            >{!! $cell->getHeader() !!}</span>
        @endif

    </th>

@endif
