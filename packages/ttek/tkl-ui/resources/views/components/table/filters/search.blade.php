@php
    /** @var \Tk\Table\Table $table */
@endphp
@aware(['table'])

@if($table->isLivewire())
    <div x-data="{ q: @entangle('search') }">
        <input type="text" class="form-control form-control-sm"
               placeholder="&#x1F50D; Search"
               x-model="q"
               @input.debounce.250ms="if (q.trim().length >= 3 || q.trim() === '') $wire.set('search', q.trim())"
        />
    </div>
@else
    <div class="input-group input-group-sm">
        <input type="text" class="form-control"
               placeholder="&#x1F50D; Search"
               id="fid-{{ $table->tablekey('search') }}"
               name="{{ $table->tableKey('search') }}"
               value="{{ request()->input($table->tableKey('search'), '') }}"
               onkeydown="if (event.keyCode === 13) { document.getElementById('fid-search-btn').click(); }"
        />
        <button class="btn btn-outline-secondary" type="submit" id="fid-search-btn">
            <i class="fa fa-search"></i>
        </button>
    </div>
@endif


