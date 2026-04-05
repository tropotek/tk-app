<?php
/** @var \Tk\Table\Table $table */
?>
@aware(['table'])

<div class="p-2 pe-0">
    <div class="limit-links input-group input-group-sm mb-2 me-1" title="Results per page">
        <label class="input-group-text" for="fid-limit"><i class="fa fa-list"></i></label>
        <button type="button" class="form-select" data-bs-toggle="dropdown">
            <span>{{ $table->limit ?: 'ALL' }}</span>
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 0]) }}">All</a>
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 10]) }}">10</a>
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 30]) }}">30</a>
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 50]) }}">50</a>
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 100]) }}">100</a>
            <a class="dropdown-item"
               href="{{ request()->fullUrlWithQuery([$table->tableKey('page') => '1', $table->tableKey('limit') => 250]) }}">250</a>
        </div>
    </div>
</div>
