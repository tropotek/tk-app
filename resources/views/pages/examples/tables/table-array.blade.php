<x-pages.main>

    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <x-tkl-ui::table.tk-filters :table="$table">
        <x-slot name="actions">
            <div class="p-2 ps-0">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </x-slot>
    </x-tkl-ui::table.tk-filters>
    <x-tkl-ui::table :table="$table"/>

</x-pages.main>
