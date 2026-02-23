@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'values' => [],
    'action' => '/formThree/submit',
    'method' => 'post',
])

<x-layout.main>
    <p>&nbsp;</p>
    <p>Use a detail template with side columns</p>

    <x-ui.double-side-col :$mode>
        <x-slot:title>Side Cols Template Example</x-slot:title>

        {{-- Buttons outside the form require the `form=""` attribute to submit the correct form --}}
        <x-slot:btnrow>
            <x-tk-base::form.buttons.default-btns
                editRoute="/formThree/edit"
                cancelRoute="/formThree"
                form="theform"
            />

            @if ($mode == 'view')
                <x-tk-base::form.buttons.link
                    label="Do Something"
                    class="btn btn-info"
                    href="/"
                />
                <x-tk-base::form.buttons.link
                    label="Do Something Else"
                    class="btn btn-warning"
                    href="/"
                />
                <x-tk-base::form.buttons.link
                    label="Delete Something"
                    class="btn btn-danger"
                    href="/"
                />
            @elseif ($mode == 'edit')
                <x-tk-base::form.buttons.link
                    label="Do Something In Edit Mode"
                    class="btn btn-info"
                    href="/"
                />
            @endif
        </x-slot:btnrow>

        <x-slot:col1>

            <x-tk-base::form :$method :$action :$mode>

                <x-slot:fields>
                    <x-tk-base::form.fields.hidden
                        name="testId"
                        id="lettitgo"
                        :value="$values['testId'] ?? ''"
                    />

                    <x-tk-base::form.ui.fieldgroup class="col">
                        <x-tk-base::form.fields.select
                            name="title"
                            :options="['' => '-- Select --', 'mr' => 'Mr', 'mrs' => 'Mrs', 'miss' => 'Miss']"
                            :value="$values['title'] ?? ''"
                        />

                        <x-tk-base::form.fields.input
                            name="firstName"
                            required="required"
                            :value="$values['firstName'] ?? ''"
                        />

                        <x-tk-base::form.fields.input
                            name="lastName"
                            :value="$values['lastName'] ?? ''"
                        />

                        <x-tk-base::form.fields.input
                            name="dob"
                            label="Date of Birth"
                            type="date"
                            fieldCss="col-sm-6"
                            :value="$values['dob'] ?? ''"
                        />

                        <x-tk-base::form.fields.textarea
                            name="description"
                            help="Hello whats up"
                            :value="$values['description'] ?? ''"
                        />
                    </x-tk-base::form.ui.fieldgroup>


                    <x-tk-base::form.ui.fieldgroup class="col">
                        <x-tk-base::form.fields.file
                            label="Upload Logo"
                            name="image"
                            help="Upload a company logo"
                            value="<a href='/' target='_blank'>A Link To The File</a>"
                        />

                        <x-tk-base::form.fields.checkbox
                            name="options[]"
                            :options="['option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3']"
                            help="Who are you..."
                            :value="$values['options'] ?? ''"
                        />
                    </x-tk-base::form.ui.fieldgroup>
                </x-slot>

            </x-tk-base::form>

        </x-slot:col1>

        <x-slot:col2>
            <div class="card mb-3 border-info">
                <div class="card-header text-bg-info">
                    <h6 class="mb-0">
                        <a href="#collapse-example1" id="heading-example" role="button" class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                            <i class="fa fa-chevron-down text-white-50 float-end"></i>
                            Collapsible Group Item #1
                        </a>
                    </h6>
                </div>
                <div id="collapse-example1" class="collapse show">
                    <div class="card-body">
                        <p class="card-text">
                            Some quick example text to build on the card title and
                            make up the bulk of the card’s content.
                        </p>
                        <p class="card-text">
                            Some quick example text to build on the card title and
                            make up the bulk of the card’s content.
                        </p>
                    </div>
                </div>
            </div>
        </x-slot:col2>

        <x-slot:col3>
            <div class="card mb-3 border-info">
                <div class="card-header text-bg-primary">
                    <h6 class="mb-0">
                        <a href="#collapse-example" id="heading-example" role="button" class="d-block text-decoration-none text-white" data-bs-toggle="collapse">
                            <i class="fa fa-chevron-down text-white-50 float-end"></i>
                            Collapsible Group Item #1
                        </a>
                    </h6>
                </div>
                <div id="collapse-example" class="collapse show">
                    <div class="card-body">
                        <p class="card-text">
                            Some quick example text to build on the card title and
                            make up the bulk of the card’s content.
                        </p>
                        <p class="card-text">
                            Some quick example text to build on the card title and
                            make up the bulk of the card’s content.
                        </p>
                    </div>
                </div>
            </div>
        </x-slot:col3>

    </x-ui.double-side-col>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</x-layout.main>
