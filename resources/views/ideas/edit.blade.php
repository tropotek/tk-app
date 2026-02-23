@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'action' => '/ideas/' . $idea->id,
    'method' => 'patch'
])
<x-layout.main>
    <h3>Create Idea</h3>

    <form action="/ideas/{{ $idea->id }}" method="POST" id="btn-delete-idea">
        @csrf
        @method('DELETE')
    </form>

    <x-tk-base::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="/ideas/{{ $idea->id }}/edit"
                cancelRoute="/ideas/{{ $idea->id }}" />
            <x-tk-base::form.buttons.submit label="Delete" form="btn-delete-idea" class="btn btn-danger" />
        </x-slot:buttons>


        <x-slot:fields>
            <x-tk-base::form.fields.input
                name="title"
                required=""
                :value="$idea->title"
            />

            <x-tk-base::form.fields.select
                name="status"
                :options="['' => '-- Select --']+\App\Enum\IdeaStatus::getLabels()"
                :value="$idea->status->value"
            />

            <x-tk-base::form.fields.textarea
                name="description"
                :value="$idea->description"
            />
        </x-slot:fields>

    </x-tk-base::form>

</x-layout.main>
