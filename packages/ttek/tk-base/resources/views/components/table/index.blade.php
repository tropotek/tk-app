<?php
/** @var \Tk\Table\Table $table */
?>
@props([
    // required
    'table',
])
@php
@endphp

<div class="tk-table-wrapper table-responsive">

    <div class="d-flex">
        <div class="p-2 ps-0 flex-grow-1">
        </div>
        <div class="p-2">
            <i class="fa-solid fa-filter fa-lg"></i>
        </div>
        <div class="p-2 pe-0">
            <select name="limit" class="form-select">
                <option value="">Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="d-flex">
        <div class="p-2 ps-0 flex-grow-1">
            <input type="text" name="search" placeholder="Search" class="form-control" />
        </div>
        <div class="p-2">
            <select name="limit" class="form-select">
                <option value="0">All</option>
                <option value="4">4</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="p-2 pe-0">
            <button type="button" class="btn btn-primary">Create</button>
        </div>
    </div>


    <table
            {{ $attributes->merge([
                'class'     => 'tk-table table table-hover',
                'id'        => $table->getId(),
            ]) }}
    >
        <thead class="table-light">
        <tr>
            @foreach ($table->getCells() as $cell)
                <x-tk-base::table.header :cell="$cell"/>
            @endforeach
        </tr>
        </thead>

        <tbody>
            @if($table->hasRecords())
                @foreach ($table->getRecords()->toArray() as $i => $row)
                    <x-tk-base::table.row :idx="$i" :$row />
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="mt-2">
        {{ $table->getPaginator() }}
    </div>
</div>
