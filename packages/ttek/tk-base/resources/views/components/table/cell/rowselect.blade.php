<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    'row',
])
@php
    $cell->addClass('text-center');
@endphp
<td {{ $attributes->merge($cell->getAttributes()->toArray()) }}>
    <input type="checkbox"
           name="{{ $cell->getName() }}[]"
           value="{{ $cell->getValue($row) }}"
           class="trs-row"
    />
</td>
