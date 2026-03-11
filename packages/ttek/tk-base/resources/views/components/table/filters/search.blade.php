<?php
/** @var \Tk\Table\Table $table */
?>
@aware(['table'])

@props([
    // required
    // optional
])

<div class="input-group input-group-sm">
    <input type="text" class="form-control" placeholder="Search"
           id="fid-{{ $table->key('search') }}"
           name="{{ $table->key('search') }}" value="{{ $table->getParam('search', '') }}"/>
    <button class="btn btn-outline-secondary" type="submit" id="fid-search-btn">
        <i class="fa fa-search"></i>
    </button>
</div>
