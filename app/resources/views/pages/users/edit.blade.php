@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'action' => '',
    'method' => 'post'
])

<x-pages.main>
    <x-layouts.single-col :$mode>
        <x-slot:pageTitle>{{ $pageName }}</x-slot:pageTitle>

        <x-slot:btnrow>

            <x-tk-base::form.buttons.default-btns
                editRoute="/user/{{ $user->id }}/edit"
                cancelRoute="/users"
                form="theform"
            />

        </x-slot:btnrow>

        <x-slot:col1>

            <x-tk-base::form :$method :$action :$mode>

                <x-slot:fields>

                    <x-tk-base::form.fields.input
                        name="name"
                        required=""
                        :value="$user->name"
                    />

                    <x-tk-base::form.fields.input
                        name="email"
                        type="email"
                        required=""
                        :value="$user->email"
                    />

                    @if ($mode == 'create')
                        <x-tk-base::form.fields.input
                            name="password"
                            required=""
                            type="password"
                        />
                    @endif

                </x-slot:fields>

            </x-tk-base::form>

        </x-slot:col1>

        <x-slot:col2>

            <div class="card mb-3 border-info">
                <div class="card-header text-bg-info">
                    <h6 class="mb-0">
                        <a href="#collapse-example1" id="heading-example" role="button" class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                            <i class="fa fa-chevron-down text-white-50 float-end"></i>
                            Permissions
                        </a>
                    </h6>
                </div>
                <div id="collapse-example1" class="collapse show">
                    <div class="card-body">
                        <p class="card-text">
                            Select this users roles and permissions
                        </p>
                        <p class="card-text">Roles</p>
                        <ul class="list-unstyled ms-4">
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                        </ul>
                        <p class="card-text">Permissions</p>
                        <ul class="list-unstyled ms-4">
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                            <li>
                                <input type="checkbox" id="fid-admin">
                                <label for="fid-admin">Admin</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </x-slot:col2>

    </x-layouts.single-col>
    <p>&nbsp;</p>

</x-pages.main>
