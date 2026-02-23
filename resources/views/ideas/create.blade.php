@props([
    'mode'   => 'create',       // ['view', 'edit', 'create']
    'action' => '/ideas',
    'method' => 'post',
])
<x-layout.main>
    <h3>Create Idea</h3>

    <x-tk-base::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="/ideas/edit"
                cancelRoute="/ideas" />
        </x-slot:buttons>


        <x-slot:fields>
            <x-tk-base::form.fields.input
                name="title"
                required=""
            />

            <x-tk-base::form.fields.select
                name="status"
                :options="['' => '-- Select --']+\App\Enum\IdeaStatus::getLabels()"
                :value="\App\Enum\IdeaStatus::PENDING->value"
            />

            <x-tk-base::form.fields.textarea
                name="description"
            />
        </x-slot:fields>

    </x-tk-base::form>

</x-layout.main>
