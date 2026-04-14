@php
    /** @var \Tk\Table\Column $column */
@endphp
@props([
    // required
    'column',
])
@if($column->isVisible())

    @php
        $wireClick = null;
        if ($column->isSortable() && $column->getTable()->isLivewire()) {
            $wireClick = $column->getTable()->getSort() === $column->getSort()
                ? 'toggleDir'
                : "setSort('" . $column->getSort() . "')";
        }
        $attributes = $attributes->merge($column->getHeaderAttrs()->all());
    @endphp

    {{-- todo mm: add an anchor tag here for non-livewire tables. --}}
    <th {{ $attributes->class([
            'fw-bold text-decoration-none' => true,
            'col-sort text-primary text-decoration-none clickable' => $column->isSortable(),
            $column->getTable()->getDir() => $column->getTable()->getSort() === $column->getSort(),
        ]) }}
        @if ($wireClick)
            wire:click="{{ $wireClick }}"
            @endif
    >
        @if($column->isSortable() && !$column->getTable()->isLivewire())
            <a class="sort-btn"
               href="{{ $column->getTable()->isLivewire() ? 'javascript:' : $column->getNextSortUrl() }}">{!! $column->getHeader() !!}</a>
        @else
            <span
                    @if($column->isSortable() && $column->getTable()->isLivewire()) class="sort-btn" @endif
            >{!! $column->getHeader() !!}</span>
        @endif

    </th>

@endif
