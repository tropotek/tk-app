<x-layout.main>
    <div class=" mb-2">
        <h2>{{ $pageTitle }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tk-base::table.filter-table :$table>

                <x-slot name="filters">
                    <x-tk-base::table.filters.select
                        :name="$table->key('status')"
                        :options="[ '' => '- Status -', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
                        value="{{ $table->getState('status') }}"
                    />
                </x-slot>

                <x-slot name="actions">
                    {{-- todo create action components --}}
                    <div class="p-2 ps-0">
                        <a class="btn btn-sm btn-outline-secondary" href="/ideas/create">
                            <i class="fa fa-plus-circle"></i> Create
                        </a>
                    </div>
                </x-slot>

            </x-tk-base::table.filter-table>
        </div>
    </div>

</x-layout.main>
