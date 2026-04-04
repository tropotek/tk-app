<?php
/** @var \Tk\Livewire\Table\Cell $cell */
?>
@props([
    // required
    'cell',
])
@php
    $attributes->merge($cell->getHeaderAttrs()->toArray());
@endphp

<th {{ $attributes->merge(['class' => $cell->getSortDir() ?: null]) }}>
    @if ($cell->isSortable())
        <a class="noblock" href="#">{!! $cell->getHeader() !!}</a>
    @else
        {!! $cell->getHeader() !!}
    @endif
</th>
