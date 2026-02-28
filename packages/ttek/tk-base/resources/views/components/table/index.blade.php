<?php
/** @var \Tk\Table\Table $table */
?>
@props([
    // required
    'table',
    // optional

])
@php

@endphp

<div class="tk-table-wrapper table-responsive">
    <table
        {{ $attributes->merge([
            'class'     => 'tk-table table table-hover',
            'id'        => $table->getId(),
        ]) }}
    >
        <thead class="table-light">
            <tr>
                @foreach ($table->getCells() as $cell)
                    <x-tk-base::table.header :cell="$cell" />
                @endforeach
            </tr>
        </thead>

        <tbody>

            @foreach ($table->getRows() as $i =>$row)
                <tr class="">
                    @foreach ($table->getCells() as $cell)
                        <x-tk-base::table.cell :$row :$cell />
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-2">
        {{ \App\Models\Idea::paginate($table->getLimit(), '[*]', $table->makeIdKey(\Tk\Table\Table::PARAM_PAGE)) }}
    </div>
</div>
