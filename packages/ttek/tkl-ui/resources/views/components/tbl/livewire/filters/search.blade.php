<?php
/** @var \Tk\Tbl\Table $table */
?>
@aware(['table'])

<div x-data="{ q: '' }">
    <input type="text" class="form-control form-control-sm"
           placeholder="&#x1F50D; Search"
           x-model="q"
           @input.debounce.250ms="if (q.trim().length >= 3 || q.trim() === '') $wire.set('search', q.trim())"
    />
</div>
