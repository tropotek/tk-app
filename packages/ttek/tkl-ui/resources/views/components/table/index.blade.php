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
                @php
                    $keyVal = \Tk\Table\Cell::getKey($row) ?: $i;
                    $keyAttr = $cell->getTable()->isLivewire() ? sprintf('wire:key="%s"', $keyVal) : sprintf('data-id="%s"', $keyVal);
                @endphp
                <tr {{ $keyAttr }}>
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
