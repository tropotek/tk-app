@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'action' => '',
    'cancelRoute' => '',
    'editRoute' => '',
    'method' => 'post'
])

<x-pages.main>

    <h3 class="mb-4">{{ $pageName }}</h3>

    <x-tkl-ui::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tkl-ui::form.buttons.default-btns
                :editRoute="$editRoute"
                :cancelRoute="$cancelRoute" />
        </x-slot:buttons>

        <x-slot:fields>

            <x-tkl-ui::form.fields.input
                name="name"
                required=""
                :value="$user->name"
            />

            <x-tkl-ui::form.fields.input
                name="email"
                type="email"
                required=""
                :value="$user->email"
            />

            <x-tkl-ui::form.fields.checkbox
                name="roles[]"
                :options="\App\Enum\Roles::toValueNameArray()"
                :value="$user->roles->pluck('name')->all()"
            />

            @if ($mode == 'create')
                <x-tkl-ui::form.fields.input
                    name="password"
                    required=""
                    type="password"
                />
            @endif

        </x-slot:fields>

    </x-tkl-ui::form>

    <p>&nbsp;</p>

</x-pages.main>
