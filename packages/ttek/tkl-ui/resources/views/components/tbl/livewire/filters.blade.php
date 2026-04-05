<?php
/** @var \Tk\Tbl\Table $table */
?>
@props([
    // required
    'table',
    // optional
    'showSearch' => true,
    'showLimit' => true,
])

<div {{ $attributes }}>

    @if (!empty($filters) && $filters->hasActualContent())
        <div class="d-flex">

            @if ((!empty($filters) && $filters->hasActualContent()) || (!empty($actions2) && $actions2->hasActualContent()))
                <div class="py-2">
                    <button
                        type="button"
                        class="btn btn-link p-0"
                        title="Clear Filters & Search"
                        wire:click="clearFilters"
                    >
                        <i class="fa-solid fa-filter align-middle"></i>
                    </button>
                </div>

                {{ $filters }}

                <button
                    type="button"
                    class="btn btn-link btn-sm"
                    title="Clear Filters & Search"
                    wire:click="clearFilters"
                >
                    <i class="fa fa-circle-xmark fa-lg"></i>
                </button>
            @endif

            <div class="py-2 flex-grow-1"></div>

            @if (!empty($actions2) && $actions2->hasActualContent())
                {{ $actions2 }}
            @endif
        </div>
    @endif


    <div class="d-flex">
        @if (!empty($actions) && $actions->hasActualContent())
            {{ $actions }}
        @endif

        <div class="py-2 flex-grow-1">
            @if ($showSearch)
                <x-tkl-ui::tbl.livewire.filters.search />
            @endif
        </div>

        @if($showLimit)
            <x-tkl-ui::tbl.livewire.filters.limit />
        @endif
    </div>

</div>
