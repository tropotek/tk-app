@php
    use Tk\Table\Filter;
    /** @var \Tk\Table\IsTable $table */
@endphp
@props([
    // required
    'table',
    // optional
    'showFilters' => true,
    'showLimit' => true,
])
@php
    $rows = $table->paginatedRows();
@endphp

<div {{ $attributes }}>
    <form method="get">

        @if($showFilters && $table->getVisibleFilters()->count())
            <div class="d-flex">
                <div class="py-2">
                    <button
                        type="{{ $table->isLivewire() ? 'button' : 'submit' }}"
                        class="btn btn-link p-0 pe-2"
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

                @foreach($table->getVisibleFilters() as $filter)
                    <div class="py-2">
                        @switch($filter->getType())
                            @case(Filter::TYPE_SELECT)
                                <x-tkl-ui::table.filters.select :filter="$filter"/>
                                @break
                            @case(Filter::TYPE_TEXT)
                                <x-tkl-ui::table.filters.text :filter="$filter"/>
                                @break
                            @case(Filter::TYPE_CHECKBOX)
                                <x-tkl-ui::table.filters.checkbox :filter="$filter"/>
                                @break
                            @case(Filter::TYPE_DATE)
                                <x-tkl-ui::table.filters.date :filter="$filter"/>
                                @break
                        @endswitch
                    </div>
                @endforeach

                {{-- Clear --}}
                <button
                    type="{{ $table->isLivewire() ? 'button' : 'submit' }}"
                    class="btn btn-link btn-sm pb-1"
                    title="Clear Filters & Search"
                    @if($table->isLivewire())
                        wire:click="clearFilters"
                    @else
                        name="{{ $table->tableKey('reset') }}"
                    value="1"
                    @endif
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
                @if($table->searchable())
                    <x-tkl-ui::table.filters.search :table="$table" />
                @endif
            </div>

            @if (!empty($rightActions) && $rightActions->hasActualContent())
                {{ $rightActions }}
            @endif

                @if($table->exportable())
                    @php $exportRoute = $table->exportRoute(); @endphp
                    @if(\Illuminate\Support\Facades\Route::has($exportRoute))
                        <div class="p-2">
                            <a
                                href="{{ route($exportRoute) }}"
                                target="_blank"
                                class="text-primary clickable align-middle"
                                title="Export CSV"
                            >
                                <i class="fa-regular fa-file-excel fa-lg"></i>
                            </a>
                        </div>
                    @else
                        @if($table->isLivewire() && method_exists($table, 'export'))
                            <div
                                class="p-2 text-primary clickable"
                                title="Export CSV"
                                wire:click="export"
                            >
                                <i class="fa-regular fa-file-excel fa-lg align-middle"></i>
                            </div>
                        @endif
                    @endif
                @endif
            @if($showLimit)
                <x-tkl-ui::table.filters.limit />
            @endif

        </div>
    </form>
</div>
