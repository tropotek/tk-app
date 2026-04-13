@php
    use Tk\Table\Cell;
    /** @var \Tk\Table\IsTable $table */
@endphp
@props([
    // required
    'table',
    // optional
    'showPaginator' => true,
])
@php
    $rows = $table->paginatedRows();
    $attributes = $attributes->merge($table->getAttrs()->all());
@endphp

<div class="table-responsive">
    <table {{ $attributes->merge(['id' => $table->tableId(), 'class' => 'table table-striped table-hover']) }}>
        <thead>
            <tr>
                @foreach ($table->getVisibleCells() as $cell)
                    <x-tkl-ui::table.th :cell="$cell"/>
                @endforeach
            </tr>
        </thead>

        <tbody class="table-group-divider">

        @forelse ($rows as $i => $row)
            @php
                $keyVal = Cell::getKey($row) ?: $i;
                $keyAttr = $table->isLivewire() ? sprintf('wire:key="%s"', $keyVal) : sprintf('data-id="%s"', $keyVal);
            @endphp
            <tr {!! $keyAttr !!} {{ $table->rowAttrs($row) }}
                {{-- Make the row ckickable when 'data-url' attr exists --}}
                x-data
                @click="
                        const ignore = $event.target.closest('a, button');
                        if (ignore) return;

                        const url = $el.dataset.url;
                        if (!url) return;

                        window.location.href = url;
                    "
            >
                @foreach($table->getVisibleCells() as $cell)
                    <x-tkl-ui::table.td :cell="$cell" :row="$row"/>
                @endforeach
            </tr>
        @empty
            <tr>
                <td
                    colspan="{{ $table->getVisibleCells()->count()  }}"
                    class="text-center text-muted py-4"
                >
                    No results found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if ($rows->perPage() > 0 && $rows->lastPage() > 1)
        <div class="mt-4">
            {{ $rows->links() }}
        </div>
{{--        <div class="mt-4 d-flex justify-content-center">--}}
{{--            {{ $rows->links('tkl-ui::components.table.paginator') }}--}}
{{--        </div>--}}
    @endif
</div>
