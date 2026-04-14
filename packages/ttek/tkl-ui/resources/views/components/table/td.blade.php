@php
    /** @var \Tk\Table\Column $column */
@endphp
@props([
    // required
    'column',
    'row'
])
@if($column->isVisible())
    <td {{ $attributes->merge($column->getAttrs()->toArray()) }}>
        {!! $column->view($row) !!}
    </td>
@endif
