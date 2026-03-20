@php
    $attributes->merge($this->getAttrs()->toArray());
@endphp
<div class="tk-table-wrap table-responsive p-1">
    <table
        {{ $attributes->merge([
            'class'     => 'tk-table table table-hover',
            'id'        => $this->id,
        ]) }}
    >
        <thead class="table-light">
        <tr>
            @foreach ($this->getCells() as $cell)
                @if($cell->componentExists($cell->getComponentHead()))
                    <x-dynamic-component :component="$cell->getComponentHead()" :$cell />
                @else
                    <x-tkl-ui::table.livewire.header :$cell />
                @endif
            @endforeach
        </tr>
        </thead>
        <tbody class="">
            @if($this->hasRecords())
                @foreach ($this->getRecords() as $i => $row)
                    <x-tkl-ui::table.livewire.row :idx="$i" :$row :table="$this" />
                @endforeach
            @endif
        </tbody>
    </table>

    @if ($limit > 0 && $this->getPaginator() && $showPaginator)

        <div class="mt-2">
            {!! str_replace ('class="pagination"', 'class="pagination pagination-sm"', $this->getPaginator()->links()) !!}
        </div>
   @endif

{{--    {!! str_replace ('class="pagination"', 'class="pagination pagination-sm"', $rows->links()) !!}--}}

</div>
