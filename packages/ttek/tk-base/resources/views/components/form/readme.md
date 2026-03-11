# Form

Create a controller and route:
```php
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    protected array $values = [
        'title' => 'mrs',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
    ];

    // route: /formExample 
    public function index(Request $request)
    {
        return view('formexample', [
            'mode' => 'view',
            'values' => $this->values,
        ]);
    }
    
    ...
}
```

Then create the Blade form template `formexample.blade.php`:
```bladehtml
@props([
    'mode'   => 'view',       // ['view', 'edit', 'create']
    'action' => '/formExample/submit',
    'method' => 'post',
])
<x-layout.main>
    <p>&nbsp;</p>

    <x-tk-base::form :$method :$action :$mode>

        <x-slot:buttons>
            <x-tk-base::form.buttons.default-btns
                editRoute="/formExample/edit"
                cancelRoute="/formExample" />
        </x-slot:buttons>

        <x-slot:fields>
            <x-tk-base::form.fields.hidden name="testId" />
            
            <x-tk-base::form.ui.fieldgroup class="col">
                <x-tk-base::form.fields.select
                    name="title"
                    :options="['' => '-- Select --', 'mr' => 'Mr', 'mrs' => 'Mrs', 'miss' => 'Miss']"
                    fieldCss="col-sm-2"
                    :value="$values['title'] ??''"
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
            </x-tk-base::form.ui.fieldgroup>

            <x-tk-base::form.ui.fieldset class="col">

                <x-tk-base::form.fields.input
                    name="email"
                    help="Select a country from across the globe"
                    :value="$values['email'] ?? ''"
                />
                
                <x-tk-base::form.fields.textarea
                    name="description"
                    help="Hello whats up"
                    :value="$values['description'] ?? ''"
                />
            </x-tk-base::form.ui.fieldset>
            
        </x-slot>

    </x-tk-base::form>

    <p>&nbsp;</p>
</x-layout.main>
```








