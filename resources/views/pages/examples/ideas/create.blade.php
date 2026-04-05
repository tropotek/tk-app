@php use App\Enum\IdeaStatus; @endphp
@props([
    'mode'   => 'create',       // ['view', 'edit', 'create']
    'action' => '/examples/ideas',
    'method' => 'post',
])
<x-pages.main>
    <h3>{{ $pageName }}</h3>

    <x-tkl-ui::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns
                    editRoute="/examples/ideas/edit"
                    cancelRoute="/examples/ideas"/>
        </x-slot:buttons>


        <x-slot:fields>
            <x-tkl-ui::form.fields.input
                    name="title"
                    required=""
            />

            <x-tkl-ui::form.fields.select
                    name="status"
                    :options="['' => '-- Select --']+IdeaStatus::getLabels()"
                    required=""
                    :value="IdeaStatus::PENDING->value"
            />

            <x-tkl-ui::form.fields.textarea
                    name="description"
                    required=""
            />
        </x-slot:fields>

    </x-tkl-ui::form>

</x-pages.main>
