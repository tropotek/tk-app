<?php
/** @var \Tk\Table\Table $table */
?>
@props([
    // required
    'table',
    // optional
    'showPaginator' => true,
])
@php
    $attributes = $attributes->merge($table->getAttributes()->all());
@endphp

<div class="tk-table-wrap table-responsive p-1">

    <table
        {{ $attributes->merge([
            'class'     => 'tk-table table table-hover',
            'id'        => $table->getId(),
        ]) }}
    >
        <thead class="table-light">
        <tr>
            @foreach ($table->getCells() as $cell)
                @if($cell->componentExists($cell->getComponentHead()))
                    <x-dynamic-component :component="$cell->getComponentHead()" :$cell/>
                @else
                    <x-tk-base::table.header :$cell/>
                @endif
            @endforeach
        </tr>
        </thead>

        <tbody>
        @if($table->hasRecords())
            @foreach ($table->getRecords()->toArray() as $i => $row)
                <x-tk-base::table.row :idx="$i" :$row/>
            @endforeach
        @endif
        </tbody>
    </table>

    @if ($table->getLimit() > 0 && $table->getPaginator() && $showPaginator)
        <div class="mt-2">
            {{ $table->getPaginator() }}
        </div>
   @endif

</div>
