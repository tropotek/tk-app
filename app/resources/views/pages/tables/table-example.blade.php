<x-pages.main>
    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tk-base::table.filter-table :$table />
        </div>
    </div>

</x-pages.main>
