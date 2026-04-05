<x-pages.main>

    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <x-tkl-ui::table.filters :table="$table">
        <x-slot name="filters">
            <x-tkl-ui::table.filters.select
                :name="$table->tableKey('status')"
                :options="[ '' => '- Status -', 'Pending' => 'Pending', 'In Progress' => 'In Progress', 'Cancelled' => 'Cancelled']"
                value="{{ request()->input($table->tableKey('status'), '') }}"
            />

            <x-tkl-ui::table.filters.select
                :name="$table->tableKey('type')"
                :options="[ '' => '- Type -', 'Biopsy' => 'Biopsy', 'Necropsy' => 'Necropsy']"
                value="{{ request()->input($table->tableKey('type'), '') }}"
            />
        </x-slot>

        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </x-slot>

    </x-tkl-ui::table.filters>

    <x-tkl-ui::table :table="$table" />

</x-pages.main>
