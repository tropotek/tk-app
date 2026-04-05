<?php
/** @var \Tk\Table\Table $table */
?>
@props([
    // required
    'table'
])

<div class="table-responsive">
    <table class="tk-table table table-hover">
        <thead class="table-light">
        <tr>
            {{-- x-prepend-head --}}
            @foreach ($table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                <x-tkl-ui::table.th :cell="$cell"/>
            @endforeach
            {{-- x-append-head --}}
        </tr>
        </thead>

        <tbody class="table-group-divider">
        @foreach ($table->rows() as $i => $row)
            {{-- TODO: not a fan of this, should get the id from a cell?  --}}
            <tr data-id="{{ $user->id ?? $i }}">
                {{-- x-prepend-cell --}}
                @foreach($table->getCells()->filter(fn($r) => $r->isVisible()) as $cell)
                    <x-tkl-ui::table.td :cell="$cell" :row="$row"/>
                @endforeach
                {{-- x-apppend-cell --}}
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! str_replace ('class="pagination"', 'class="pagination pagination-sm"', $table->rows()->links()) !!}
</div>
