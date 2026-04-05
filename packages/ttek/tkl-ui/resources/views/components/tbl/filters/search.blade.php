<?php
/** @var \Tk\Tbl\Table $table */
?>
@aware(['table'])

<div class="input-group input-group-sm">
    <input type="text" class="form-control" placeholder="Search"
           id="fid-{{ $table->tablekey('search') }}"
           name="{{ $table->tableKey('search') }}"
           value="{{ request()->input($table->tableKey('search'), '') }}"
           onkeydown="if (event.keyCode === 13) { document.getElementById('fid-search-btn').click(); }"
    />
    <button class="btn btn-outline-secondary" type="submit" id="fid-search-btn">
        <i class="fa fa-search"></i>
    </button>
</div>
