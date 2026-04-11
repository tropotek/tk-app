@php
    /** @var \Tk\Table\Cell $cell */
@endphp
@props([
    // required
    'cell',
    'row'
])
@if($cell->isVisible())
    <td {{ $attributes->merge($cell->getAttrs()->toArray()) }}>
        {!! $cell->view($row) !!}
    </td>
@endif
