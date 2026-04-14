@php
    use Tk\Table\Filter;
    /** @var \Tk\Table\Traits\IsTable $table */
@endphp
@props([
    // required
    'table',
    // optional
    'showSearch' => true,
    'showFilters' => true,
])
@php
    $rows = $table->paginatedRows();
@endphp

<form method="get" @if(!$table->isLivewire()) action="{{ url()->current() }}" @endif>
    @if(!$table->isLivewire())
        @php
            // Preserve all query params that this form does not own as hidden inputs.
            // The form submits its own filter/search inputs, so exclude those to avoid duplicates.
            // Also drop the page param so filtering resets to page 1.
            $skipKeys = [
                $table->tableKey($table::QUERY_FILTER),
                $table->tableKey($table::QUERY_SEARCH),
                $table->tableKey($table::QUERY_PAGE),
                $table->tableKey($table::QUERY_RESET),
            ];
            $preserved = collect(request()->query())->except($skipKeys)->all();
        @endphp
        @foreach($preserved as $key => $value)
            @if(is_array($value))
                @foreach($value as $subKey => $subValue)
                    <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    @endif

    <div {{ $attributes->merge(['class' => 'd-flex flex-wrap gap-2 align-items-center my-2']) }}>

        @if($table->searchable())
            <x-tkl-ui::table.filters.search :table="$table"/>
        @endif

        @if($showFilters && $table->getVisibleFilters()->count())
            <div class="text-nowrap text-primary"><small>Filter By:</small></div>

            @foreach($table->getVisibleFilters() as $filter)
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
            @endforeach

        @endif

        @if(($showFilters && $table->getVisibleFilters()->count()) || $showSearch)
            {{-- Clear --}}
            <button
                type="{{ $table->isLivewire() ? 'button' : 'submit' }}"
                class="btn btn-link btn-sm pb-1"
                title="Clear Filters & Search"
                @if($table->isLivewire())
                    wire:click="clearFilters"
                @else
                    name="{{ $table->tableKey($table::QUERY_RESET) }}"
                value="1"
                @endif
            >
                <i class="fa fa-circle-xmark fa-lg"></i>
            </button>
        @endif

        {{-- Per-page injected header actions (New button etc) --}}
        @if (!empty($actions) && $actions->hasActualContent())
            {{ $actions }}
        @endif

        {{-- Count + export --}}
        <div class="flex-grow-1 text-end small">

            @if($rows->total() > 0)
                @if($table->exportable())
                    @php $exportRoute = $table->exportRoute(); @endphp
                    @if(\Illuminate\Support\Facades\Route::has($exportRoute))
                        <a
                            href="{{ route($exportRoute) }}"
                            target="_blank"
                            class="btn btn-link btn-sm"
                            title="Download CSV"
                        >
                            <i class="fa-regular fa-file-excel fa-lg"></i>
                        </a>
                    @else
                        @if($table->isLivewire() && method_exists($table, 'export'))
                            <button
                                type="button"
                                class="btn btn-link btn-sm"
                                title="Download CSV"
                                wire:click="export"
                            >
                                <i class="fa-regular fa-file-excel fa-lg"></i>
                            </button>
                        @endif
                    @endif
                @endif
                <span class="text-secondary">
                    Showing
                    <span class="fw-semibold">{{ $rows->firstItem() }}</span>
                    to
                    <span class="fw-semibold">{{ $rows->lastItem() }}</span>
                    of
                    <span class="fw-semibold">{{ $table->totalRows ?? $rows->total() }}</span>
                </span>
            @endif
        </div>

    </div>
</form>
