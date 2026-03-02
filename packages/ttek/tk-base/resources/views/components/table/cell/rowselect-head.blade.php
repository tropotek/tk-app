<?php
/** @var \Tk\Table\Cell $cell */
?>
@props([
    // required
    'cell',
    // optional
])
@php
    $attributes->merge($cell->getHeaderAttrs()->toArray());
@endphp

<th {{ $attributes->merge(['class' => 'text-center ' . ($cell->getOrderByDir() ?: null)]) }}>
    <input type="checkbox"
           name="{{ $cell->getName() }}_all"
           title="Select All"
           class="trs-head"
           data-trs-name="{{ $cell->getName() }}"
    />
</th>
