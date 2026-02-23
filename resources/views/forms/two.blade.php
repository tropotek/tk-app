@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'values' => [],
    'action' => '/formTwo/submit',
    'method' => 'post',
])

<x-layout.main>
    <p>&nbsp;</p>
    <p>
        By using the <code>form.ui.fieldgroup</code> template to contain the fields, we can create
        field columns dynamically leveraging the bootstrap <code>col</code> class to size the columns as needed.
    </p>
    <p>&nbsp;</p>

    <x-tk-base::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="/formTwo/edit"
                cancelRoute="/formTwo" />
        </x-slot:buttons>

        <x-slot:fields>
            <x-tk-base::form.fields.hidden
                name="testId"
                :value="$values['testId'] ?? ''"
            />


            <x-tk-base::form.ui.fieldgroup class="col">
                <x-tk-base::form.fields.select
                    name="title"
                    :options="['' => '-- Select --', 'mr' => 'Mr', 'mrs' => 'Mrs', 'miss' => 'Miss']"
                    fieldCss="col-sm-2"
                    :value="$values['title'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="firstName"
                    fieldCss="col-sm-5"
                    required=""
                    :value="$values['firstName'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="lastName"
                    fieldCss="col-sm-5"
                    :value="$values['lastName'] ?? ''"
                />

                <x-tk-base::form.fields.select
                    name="gender"
                    :options="['' => '-- Select --', 'male' => 'Male', 'female' => 'Female']"
                    help="Who are you..."
                    fieldCss="col-sm-6"
                    :value="$values['gender'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="dob"
                    label="Date of Birth"
                    type="date"
                    fieldCss="col-sm-6"
                    :value="$values['dob'] ?? ''"
                />

                <x-tk-base::form.fields.input
                    name="email"
                    help="Select a country from across the globe"
                    :value="$values['email'] ?? ''"
                />

                <x-tk-base::form.fields.checkbox
                    name="best"
                    label="Is this the best checkbox switch in the world?"
                    :options="['1' => 'Yes']"
                    :isSwitch="true"
                    :value="$values['best'] ?? ''"
                />
            </x-tk-base::form.ui.fieldgroup>


            <x-tk-base::form.ui.fieldset legend="Ha hahaha" class="col">
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
                    fieldCss="col-sm-6"
                    :value="$values['options'] ?? ''"
                />

                <x-tk-base::form.fields.radio
                    name="options2"
                    :options="['option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3']"
                    help="Who are you..."
                    fieldCss="col-sm-6"
                    :value="$values['options2'] ?? ''"
                />
            </x-tk-base::form.ui.fieldset>


            {{-- Render this field outside a field group --}}
            <x-tk-base::form.fields.textarea
                name="description"
                help="Hello whats up"
                :value="$values['description'] ?? ''"
            />
        </x-slot>

    </x-tk-base::form>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</x-layout.main>
