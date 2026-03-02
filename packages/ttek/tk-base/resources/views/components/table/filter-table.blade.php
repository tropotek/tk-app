@php use Tk\Table\Table; @endphp
<?php
/** @var Table $table */
?>
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
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search"
                            id="fid-{{ $table->key('search') }}"
                            name="{{ $table->key('search') }}" value="{{ $table->getState('search', '') }}"/>
                        <button class="btn btn-outline-secondary" type="submit" id="fid-search-btn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                @endif
            </div>

            @if($showLimit)
                <div class="p-2 pe-0">
                    <div class="limit-links input-group input-group-sm mb-2 me-1" title="Results per page">
                        <label class="input-group-text" for="fid-limit"><i class="fa fa-list"></i></label>
                        <button type="button" class="form-select" data-bs-toggle="dropdown">
                            <span>{{ $table->getLimit() ?: 'ALL' }}</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '0']) }}">All</a>
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '3']) }}">3</a>
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '10']) }}">10</a>
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '25']) }}">25</a>
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '50']) }}">50</a>
                            <a class="dropdown-item"
                                href="{{ $table->url([Table::QUERY_PAGE => '1', Table::QUERY_LIMIT => '100']) }}">100</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-tk-base::table :table="$table"/>

    </form>
</div>
