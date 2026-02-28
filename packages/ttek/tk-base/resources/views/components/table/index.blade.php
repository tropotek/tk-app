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
                <x-tk-base::table.header :cell="$cell"/>
            @endforeach
        </tr>
        </thead>

        <tbody>

        @if($table->hasRecords())
            @foreach ($table->getRecords()->toArray() as $i => $row)
                <tr class="todo-">
                    @foreach ($table->getCells() as $cell)
                        <x-tk-base::table.cell :$row :$cell/>
                    @endforeach
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <div class="mt-2">
        {{ $table->getPaginator() }}
    </div>
</div>
