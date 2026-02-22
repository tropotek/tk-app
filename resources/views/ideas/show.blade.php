<x-layout.main>


    <h3>My Idea</h3>
    <div class="">
        {{ $idea->description }} <em>[{{ $idea->status->label() }}]</em>
    </div>
    <div class="mt-4">
        <a href="/ideas/{{ $idea->id }}/edit" class="btn btn-sm btn-outline-primary">Edit Idea</a>
        <a href="/ideas" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>

</x-layout.main>