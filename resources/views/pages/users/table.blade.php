<x-pages.main>

    @if($table->count())
        <div class="mt-2">
            <h2>{{ $pageName }}</h2>

            <x-tkl-ui::table.filter-table :$table>

{{--                        <x-slot name="filters">--}}
{{--                            <x-tkl-ui::table.filters.select--}}
{{--                                    :name="$table->key('status')"--}}
{{--                                    :options="['' => '- Status -', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"--}}
{{--                                    value="{{ $table->getParam('status') }}"--}}
{{--                            />--}}
{{--                        </x-slot>--}}

                <x-slot name="actions">
                    <div class="p-2 ps-0">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $create }}">
                            <i class="fa fa-plus-circle"></i> Create
                        </a>
                    </div>
                </x-slot>

            </x-tkl-ui::table.filter-table>
        </div>
    @endif

</x-pages.main>
