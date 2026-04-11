@php
    /** @var \Tk\Table\Table $table */
@endphp
@props([
    // required
    'table',
    // optional
    'showSearch' => true,
    'showLimit' => true,
])

<div {{ $attributes }}>
{{--    <form method="get" action="{{ request()->fullUrl() }}">--}}
    <form method="get">

        @if (!empty($filters) && $filters->hasActualContent())
            <div class="d-flex">
                <div class="py-2">
                    <button
                        type="{{ $table->isLivewire() ? 'button' : 'submit' }}"
                        class="btn btn-link p-0"
                        title="Clear Filters & Search"
                        @if($table->isLivewire())
                            wire:click="clearFilters"
                        @else
                            name="{{ $table->tableKey('reset') }}"
                            value="1"
                        @endif
                    >
                        <i class="fa-solid fa-filter align-middle"></i>
                    </button>
                </div>

                {{ $filters }}

                <button
                    type="{{ $table->isLivewire() ? 'button' : 'submit' }}"
                    class="btn btn-link btn-sm"
                    title="Clear Filters & Search"
                    name="{{ $table->tableKey('reset') }}"
                    value="1"
                    @if($table->isLivewire()) wire:click="clearFilters" @endif
                >
                    <i class="fa fa-circle-xmark fa-lg"></i>
                </button>

                <div class="py-2 flex-grow-1"></div>
            </div>
        @endif

        <div class="d-flex">
            @if (!empty($actions) && $actions->hasActualContent())
                {{ $actions }}
            @endif

            <div class="py-2 flex-grow-1">
                @if ($showSearch)
                    <x-tkl-ui::table.filters.search/>
                @endif
            </div>

            @if($showLimit)
                <x-tkl-ui::table.filters.limit/>
            @endif
        </div>

    </form>
</div>
