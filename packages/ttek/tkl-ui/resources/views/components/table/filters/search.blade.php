@php
    /** @var \Tk\Table\IsTable $table */
@endphp
@aware(['table'])

{{--
    todo mm: combine these into a single element using attributes
--}}
@if($table->isSearchable())
    @if($table->isLivewire())
        <div x-data="{ q: @entangle('search') }">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control form-control-sm"
                       placeholder="{{ $table->searchPlaceholder }}"
                       x-model="q"
                       @input.debounce.250ms="if (q.trim().length >= 3 || q.trim() === '') $wire.set('search', q.trim())"
                />
            </div>
        </div>
    @else
        <div class="input-group input-group-sm">
            <input type="text" class="form-control"
                   placeholder="{{ $table->searchPlaceholder }}"
                   name="{{ $table->tableKey('search') }}"
                   value="{{ request()->input($table->tableKey('search'), '') }}"
                   onkeydown="if (event.keyCode === 13) { event.preventDefault(); this.form?.requestSubmit(); }"
    {{-- todo mm: test these and check if they are even needed (compare with sisv1) --}}
    {{--               oninput="clearTimeout(this._searchDebounce); this._searchDebounce = setTimeout(() => { if (this.value.trim().length >= 3 || this.value.trim() === '') { this.form?.requestSubmit(); } }, 250)"--}}
    {{--               onblur="if (this.value.trim().length >= 3) { this.form?.requestSubmit(); }"--}}
            />
            <button class="btn btn-outline-secondary" type="submit" id="fid-search-btn">
                <i class="fa fa-search"></i>
            </button>
        </div>
    @endif
@endif
