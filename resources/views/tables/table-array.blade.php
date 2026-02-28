<x-layout.main>

    <div class=" mb-2">
        <h2>{{ $pageTitle }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tk-base::table :table="$table1" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tk-base::table :table="$table2" />
        </div>
    </div>

</x-layout.main>
