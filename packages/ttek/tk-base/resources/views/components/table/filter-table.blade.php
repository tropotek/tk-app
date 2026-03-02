@php use Tk\Table\Table; @endphp
@props([
    // required
    'table',
    // optional
    'showSearch' => true,
    'showLimit' => true,
    'filters',
    'actions',
])

<div {{ $attributes }}>
    <form method="get" action="{{ request()->fullUrl() }}">
        {{-- required for when the table id is not set --}}
        <input type="hidden" name="{{ Table::QUERY_ID }}" value="{{ $table->getId() }}"/>

        @if (!empty($filters) && $filters->hasActualContent())
            <div class="d-flex">
                <div class="p-2 ps-0">
                    <a title="Click to reset table filters" href="{{ $table->url($table->resetUrl(), [Table::QUERY_RESET => '1']) }}">
                        <i class="fa-solid fa-filter align-middle"></i>
                    </a>
                </div>

                {{ $filters }}

                <div class="p-2 pe-0 flex-grow-1">
                </div>
            </div>
        @endif

        <div class="d-flex">
            @if (!empty($actions) && $actions->hasActualContent())
                {{ $actions }}
            @endif

            <div class="p-2 flex-grow-1">
                @if ($showSearch)
                    <x-tk-base::table.filters.search />
                @endif
            </div>

            @if($showLimit)
                <x-tk-base::table.filters.limit />
            @endif
        </div>

        <x-tk-base::table :table="$table"/>

    </form>
</div>
