<div>
    <form method="get" action="#">
        {{-- required for when the table id is not set --}}
{{--        <input type="hidden" name="{{ Table::QUERY_ID }}" value="{{ $table->id }}"/>--}}

{{--        @if (!empty($filters) && $filters->hasActualContent())--}}
{{--            <div class="d-flex">--}}
{{--                <div class="p-2 ps-0">--}}
{{--                    <a title="Click to reset table filters" href="#">--}}
{{--                        <i class="fa-solid fa-filter align-middle"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                {{ $filters }}--}}

{{--                <div class="p-2 pe-0 flex-grow-1">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

        <div class="d-flex">
            @if (!empty($actions) && $actions->hasActualContent())
                {{ $actions }}
            @endif

            <div class="p-2 flex-grow-1">
                @if ($showSearch)
{{--                    <x-tkl-ui::table.filters.search />--}}
                @endif
            </div>

            @if($showLimit)
{{--                <x-tkl-ui::table.filters.limit />--}}
            @endif
        </div>

{{--        @include('tkl-ui::livewire.table.index', [--}}
{{--            'rows' => $rows,--}}
{{--            'columns' => $columns,--}}
{{--        ])--}}

    </form>
</div>
