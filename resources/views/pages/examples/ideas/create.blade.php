@props([
    'mode'   => 'create',       // ['view', 'edit', 'create']
    'action' => '/ideas',
    'method' => 'post',
])
<x-pages.main>
    <h3>{{ $pageName }}</h3>

    <x-tkl-ui::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns
                editRoute="/ideas/edit"
                cancelRoute="/ideas" />
        </x-slot:buttons>


        <x-slot:fields>
            <x-tkl-ui::form.fields.input
                name="title"
                required=""
            />

            <x-tkl-ui::form.fields.select
                name="status"
                :options="['' => '-- Select --']+\App\Enum\IdeaStatus::getLabels()"
                required=""
                :value="\App\Enum\IdeaStatus::PENDING->value"
            />

            <x-tkl-ui::form.fields.textarea
                name="description"
                required=""
            />
        </x-slot:fields>

    </x-tkl-ui::form>

</x-pages.main>
