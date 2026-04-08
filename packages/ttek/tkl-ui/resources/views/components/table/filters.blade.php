<?php
/** @var \Tk\Table\Table $table */
?>
@props([
    // required
    'table',
    // optional
    'showSearch' => true,
    'showLimit' => true,
])

<div {{ $attributes }}>
    <form method="get" action="{{ request()->fullUrl() }}">

        @if (!empty($filters) && $filters->hasActualContent())
            <div class="d-flex">
                <div class="py-2">
                    @if($table->isLivewire())
                        <button
                            type="button"
                            class="btn btn-link p-0"
                            title="Clear Filters & Search"
                            wire:click="clearFilters"
                        >
                            <i class="fa-solid fa-filter align-middle"></i>
                        </button>
                    @else
                        {{-- TODO --}}
                        {{-- <button type="submit" class="btn btn-primary" name="{{ $table->tableKey('reset') }}" value="1"><i class="fa-solid fa-filter align-middle"></i></button> --}}
                        <a title="Click to reset table filters"
                           href="{{ request()->fullUrlWithQuery([$table->tableKey('reset') => '1']) }}">
                            <i class="fa-solid fa-filter align-middle"></i>
                        </a>
                    @endif

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
