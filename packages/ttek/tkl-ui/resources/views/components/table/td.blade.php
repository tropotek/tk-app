@php
    /** @var \Tk\Table\Table $table */
@endphp
@props([
    // required
    'cell',
    'row'
])
@if($cell->isVisible())
    <td {{ $attributes->merge($cell->getAttrs()->toArray()) }}>
        @if (!empty($slot) && $slot->hasActualContent())
            {{ $slot }}
        @else
            {!! $cell->html($row) !!}
        @endif
    </td>
@endif
