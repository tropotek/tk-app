<x-pages.main>
    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tkl-ui::table.filter-table :$table>

                <x-slot name="filters">
                    <x-tkl-ui::table.filters.select
                        :name="$table->key('status')"
                        :options="[ '' => '- Status -', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
                        value="{{ $table->getParam('status') }}"
                    />
                </x-slot>

                <x-slot name="actions">
                    {{-- todo create action components --}}
                    <div class="p-2 ps-0">
                        <a class="btn btn-sm btn-outline-secondary" href="/ideas/create">
                            <i class="fa fa-plus-circle"></i> Create
                        </a>
                    </div>
                    <div class="p-2 ps-0">
                        <button type="submit" name="tbl_delete" value="{{ $table->getId() }}" class="btn btn-sm btn-danger">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </x-slot>

            </x-tkl-ui::table.filter-table>
        </div>
    </div>

</x-pages.main>
