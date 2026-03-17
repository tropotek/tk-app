<x-pages.main>

    <div class=" mb-2">
        <h2>{{ $pageName }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tkl-ui::table :table="$table1" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tkl-ui::table :table="$table2" />
        </div>
    </div>

</x-pages.main>
