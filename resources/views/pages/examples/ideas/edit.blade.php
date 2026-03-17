@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'action' => '/ideas/' . $idea->id,
    'method' => 'patch'
])
<x-pages.main>
    <h3>{{ $pageName }}</h3>

    <form action="/ideas/{{ $idea->id }}" method="POST" id="btn-delete-idea">
        @csrf
        @method('DELETE')
    </form>

    <x-tkl-ui::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns
                editRoute="/ideas/{{ $idea->id }}/edit"
                cancelRoute="/ideas" />
            <x-tkl-ui::form.buttons.submit label="Delete" form="btn-delete-idea" class="btn btn-danger" />
        </x-slot:buttons>


        <x-slot:fields>
            <x-tkl-ui::form.fields.input
                name="title"
                required=""
                :value="$idea->title"
            />

            <x-tkl-ui::form.fields.select
                name="status"
                :options="['' => '-- Select --']+\App\Enum\IdeaStatus::getLabels()"
                :value="$idea->status->value"
            />

            <x-tkl-ui::form.fields.textarea
                name="description"
                :value="$idea->description"
            />
        </x-slot:fields>

    </x-tkl-ui::form>

</x-pages.main>
