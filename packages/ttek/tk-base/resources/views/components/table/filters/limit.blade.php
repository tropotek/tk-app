@php use Tk\Table\Table; @endphp
@aware(['table'])

@props([
    // required
    // optional
])

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
