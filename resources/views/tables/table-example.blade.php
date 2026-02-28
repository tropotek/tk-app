<x-layout.main>

    <div class=" mb-2">
        <h2>{{ $pageTitle }}</h2>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-tk-base::table :$table />
        </div>
    </div>

</x-layout.main>
