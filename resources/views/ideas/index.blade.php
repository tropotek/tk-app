<x-layout.main>

    <div class="row">
        <div class="col-lg-9 col-md-8 col-sm-6 col-xs-12">
            @if($table->count())
                <div class="mt-2">
                    <h2>My Ideas</h2>

                    <x-tk-base::table.filter-table :$table>

                        <x-slot name="filters">
                            <x-tk-base::table.filters.select
                                :name="$table->key('status')"
                                :options="['' => '- Status -', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
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
            @endif

            <div class="mt-3">
                <a href="/ideas/create" class="btn btn-sm btn-outline-primary">Create New Idea</a>
                <a href="/delete-all" class="btn btn-sm btn-outline-danger">Clear All</a>
            </div>

        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-10">

            <div class="mt-2">
                <p class="text-muted"><strong>My Ideas</strong></p>

                <ul class="list-group mt-3">
                    @foreach ($ideas as $idea)
                        <li class="list-group-item">
                            <a href="/ideas/{{ $idea->id }}">
                                {{ $idea->description }} <em class="d-inline-block float-end">[{{ $idea->status->label() }}]</em>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="modal fade" id="idea-dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Idea</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layout.main>
